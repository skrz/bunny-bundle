<?php
namespace Skrz\Bundle\BunnyBundle\Command;

use Bunny\Channel;
use Bunny\Client;
use Bunny\Message;
use Bunny\Protocol\MethodBasicQosOkFrame;
use Bunny\Protocol\MethodQueueBindOkFrame;
use Bunny\Protocol\MethodQueueDeclareOkFrame;
use Skrz\Bundle\BunnyBundle\Annotation\Consumer;
use Skrz\Bundle\BunnyBundle\BunnyException;
use Skrz\Bundle\BunnyBundle\BunnyManager;
use Skrz\Bundle\BunnyBundle\ContentTypes;
use Skrz\Meta\JSON\JsonMetaInterface;
use Skrz\Meta\MetaInterface;
use Skrz\Meta\Protobuf\ProtobufMetaInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ConsumerCommand extends Command
{

	/** @var ContainerInterface */
	private $container;

	/** @var BunnyManager */
	private $manager;

	/** @var Consumer[][] */
	private $consumers;

	/** @var int */
	private $messages = 0;

	public function __construct(ContainerInterface $container, BunnyManager $manager, array $consumers)
	{
		parent::__construct("bunny:consumer");
		$this->container = $container;
		$this->manager = $manager;
		$this->consumers = [];
		foreach ($consumers as $consumerName => $annotations) {
			$this->consumers[$consumerName] = [];
			foreach ($annotations as $annotation) {
				$this->consumers[$consumerName][] = Consumer::fromArray($annotation);
			}
		}
	}

	protected function configure()
	{
		$this
			->setDescription("Starts given consumer.")
			->addArgument("consumer-name", InputArgument::REQUIRED, "Name of consumer.")
			->addArgument("consumer-parameters", InputArgument::IS_ARRAY, "Argv input to consumer.", []);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$consumerName = strtolower($input->getArgument("consumer-name"));

		if (!isset($this->consumers[$consumerName])) {
			throw new \InvalidArgumentException("Consumer '{$consumerName}' doesn't exists.");
		}

		$consumerArgv = $input->getArgument("consumer-parameters");
		array_unshift($consumerArgv, $consumerName);

		$this->manager->setUp();

		$channel = $this->manager->getChannel();
		$consumer = $this->container->get($this->consumers[$consumerName][0]->name);
		$maxMessages = PHP_INT_MAX;
		$maxSeconds = PHP_INT_MAX;
		$calledSetUps = [];
		$tickMethod = null;
		$tickSeconds = null;

		foreach ($this->consumers[$consumerName] as $consumerSpec) {
			$maxMessages = min($maxMessages, $consumerSpec->maxMessages ?: PHP_INT_MAX);
			$maxSeconds = min($maxSeconds, $consumerSpec->maxSeconds ?: PHP_INT_MAX);

			if (empty($consumerSpec->queue)) {
				$queueOk = $channel->queueDeclare("", false, false, true);
				if (!($queueOk instanceof MethodQueueDeclareOkFrame)) {
					throw new BunnyException("Could not declare anonymous queue.");
				}

				$consumerSpec->queue = $queueOk->queue;

				$bindOk = $channel->queueBind($consumerSpec->queue, $consumerSpec->exchange, $consumerSpec->routingKey);
				if (!($bindOk instanceof MethodQueueBindOkFrame)) {
					throw new BunnyException("Could not bind anonymous queue.");
				}
			}

			if ($consumerSpec->prefetchSize || $consumerSpec->prefetchCount) {
				$qosOk = $channel->qos($consumerSpec->prefetchSize, $consumerSpec->prefetchCount);
				if (!($qosOk instanceof MethodBasicQosOkFrame)) {
					throw new BunnyException("Could not set prefetch-size/prefetch-count.");
				}
			}

			$meta = null;
			if ($consumerSpec->meta) {
				/** @var MetaInterface $metaClassName */
				$metaClassName = $consumerSpec->meta;

				if (!class_exists($metaClassName)) {
					throw new BunnyException("Consumer meta class {$metaClassName} does not exist.");
				}

				if (!method_exists($metaClassName, "getInstance")) {
					throw new BunnyException("Method {$metaClassName}::getInstance() does not exist.");
				}

				$meta = $metaClassName::getInstance();
			}

			if ($consumerSpec->setUpMethod && !isset($calledSetUps[$consumerSpec->setUpMethod])) {
				if (!method_exists($consumer, $consumerSpec->setUpMethod)) {
					throw new BunnyException(
						"Init method " . get_class($consumer) . "::{$consumerSpec->setUpMethod} does not exist."
					);
				}

				$consumer->{$consumerSpec->setUpMethod}($channel, $channel->getClient(), $consumerArgv);
				$calledSetUps[$consumerSpec->setUpMethod] = true;
			}

			if ($consumerSpec->tickMethod) {
				if ($tickMethod) {
					if ($consumerSpec->tickMethod !== $tickMethod) {
						throw new BunnyException(
							"Only single tick method is supported - " . get_class($consumer) . "."
						);
					}

					if ($consumerSpec->tickSeconds !== $tickSeconds) {
						throw new BunnyException(
							"Only single tick seconds is supported - " . get_class($consumer) . "."
						);
					}

				} else {
					if (!$consumerSpec->tickSeconds) {
						throw new BunnyException(
							"If you specify 'tickMethod', you have to specify 'tickSeconds' - " . get_class($consumer) . "."
						);
					}

					if (!method_exists($consumer, $consumerSpec->tickMethod)) {
						throw new BunnyException(
							"Tick method " . get_class($consumer) . "::{$consumerSpec->tickMethod} does not exist."
						);
					}

					$tickMethod = $consumerSpec->tickMethod;
					$tickSeconds = $consumerSpec->tickSeconds;
				}
			}

			$channel->consume(function (Message $message, Channel $channel, Client $client) use ($consumer, $consumerSpec, $meta) {
				$this->handleMessage($consumer, $consumerSpec, $meta, $message, $channel, $client);
			}, $consumerSpec->queue, $consumerSpec->consumerTag, $consumerSpec->noLocal, $consumerSpec->noAck, $consumerSpec->exclusive, false, $consumerSpec->arguments);
		}

		$startTime = microtime(true);

		while (microtime(true) < $startTime + $maxSeconds && $this->messages < $maxMessages) {
			$channel->getClient()->run($tickSeconds ?: $maxSeconds);
			if ($tickMethod) {
				$consumer->{$tickMethod}($channel, $channel->getClient());
			}
		}
		$channel->getClient()->disconnect();
	}

	public function handleMessage($consumer, Consumer $consumerSpec, $meta = null, Message $message, Channel $channel, Client $client)
	{
		$data = $message->content;
		if ($meta) {
			switch ($message->getHeader("content-type")) {
				case ContentTypes::APPLICATION_JSON:
					if ($meta instanceof JsonMetaInterface) {
						$data = $meta->fromJson($data);
					} else {
						throw new BunnyException("Meta class does not support JSON.");
					}
					break;

				case ContentTypes::APPLICATION_PROTOBUF:
					if ($meta instanceof ProtobufMetaInterface) {
						$data = $meta->fromProtobuf($data);
					} else {
						throw new BunnyException("Meta class does not support Protobuf.");
					}
					break;

				default:
					throw new BunnyException("Message does not have 'content-type' header, cannot deserialize data.");
			}
		}

		$consumer->{$consumerSpec->method}($data, $message, $channel, $client);

		if ($consumerSpec->maxMessages !== null && ++$this->messages >= $consumerSpec->maxMessages) {
			$client->stop();
		}
	}

}
