<?php

declare(strict_types=1);

namespace Skrz\Bundle\BunnyBundle\Command;

use Bunny\Channel;
use Bunny\Client;
use Bunny\Message;
use Bunny\Protocol\MethodBasicQosOkFrame;
use Bunny\Protocol\MethodQueueBindOkFrame;
use Bunny\Protocol\MethodQueueDeclareOkFrame;
use InvalidArgumentException;
use Skrz\Bundle\BunnyBundle\Exception\BunnyException;
use Skrz\Bundle\BunnyBundle\Queue\BunnyConsumerInterface;
use Skrz\Bundle\BunnyBundle\Queue\BunnyTickingConsumer;
use Skrz\Bundle\BunnyBundle\Queue\BunnyTickingConsumerInterface;
use Skrz\Bundle\BunnyBundle\Service\BunnyManager;
use Skrz\Bundle\BunnyBundle\Service\ContentTypes;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

/** @author Lukas Senfeld <skrz@senfeld.net> */
class ConsumerCommand extends Command
{

	/** @var BunnyConsumerInterface[] */
	private iterable $consumers;
	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
	 * @var string|null The default command name
	 */
	protected static $defaultName = 'bunny:consumer';
	private BunnyManager $manager;
	private int $messages = 0;
	private SerializerInterface $serializer;

	/** @param BunnyConsumerInterface[] $consumers */
	public function __construct(BunnyManager $manager, SerializerInterface $serializer, iterable $consumers)
	{
		$this->manager = $manager;

		$this->consumers = [];

		foreach ($consumers as $consumer) {
			$consumer->configure();
			$this->consumers[$consumer->getName()] = $consumer;
		}

		$this->serializer = $serializer;
		parent::__construct();
	}

	protected function configure(): void
	{
		$this
			->setHelp("Available consumers are:\n" . implode(PHP_EOL, array_keys($this->consumers)))
			->setDescription("Starts given consumer.")
			->addArgument("consumer-name", InputArgument::REQUIRED, "Name of consumer.")
			->addArgument("consumer-parameters", InputArgument::IS_ARRAY, "Argv input to consumer.", []);
	}

	protected function execute(InputInterface $input, OutputInterface $output): void
	{
		$consumerName = $input->getArgument("consumer-name");

		if (!isset($this->consumers[$consumerName])) {
			throw new InvalidArgumentException("Consumer '{$consumerName}' doesn't exists.");
		}

		$consumerArgv = $input->getArgument("consumer-parameters");
		array_unshift($consumerArgv, $consumerName);

		$this->manager->setUp();

		$channel = $this->manager->getChannel();

		if ($channel === null) {
			throw new BunnyException("Could not open channel.");
		}

		$consumer = $this->consumers[$consumerName];

		if ($consumer->getQueue() === null) {
			$queueOk = $channel->queueDeclare("", false, false, true);

			if (!($queueOk instanceof MethodQueueDeclareOkFrame)) {
				throw new BunnyException("Could not declare anonymous queue.");
			}

			$consumer->setQueue($queueOk->queue);

			$bindOk = $channel->queueBind($consumer->getQueue(), $consumer->getExchange(), $consumer->getRoutingKey());

			if (!($bindOk instanceof MethodQueueBindOkFrame)) {
				throw new BunnyException("Could not bind anonymous queue.");
			}
		}

		if ($consumer->getPrefetchSize() || $consumer->getPrefetchCount()) {
			$qosOk = $channel->qos($consumer->getPrefetchSize(), $consumer->getPrefetchCount());

			if (!($qosOk instanceof MethodBasicQosOkFrame)) {
				throw new BunnyException("Could not set prefetch-size/prefetch-count.");
			}
		}

		if ($consumer instanceof BunnyTickingConsumerInterface && $consumer->getTickSeconds() === null) {
			throw new BunnyException(
				sprintf(
					"If you implement 'TickerConsumerInterface', you have to specify 'tickSeconds' - %s.",
					get_class($consumer)
				)
			);
		}

		$channel->consume(
			function (Message $message, Channel $channel, Client $client) use ($consumer): void {
				$this->handleMessage($consumer, $message, $channel, $client);
			},
			$consumer->getQueue(),
			$consumer->getConsumerTag(),
			$consumer->isNoLocal(),
			$consumer->isNoAck(),
			$consumer->isExclusive(),
			false,
			$consumer->getArguments()
		);

		$startTime = microtime(true);
		$maxMessages = $consumer->getMaxMessages() ?: PHP_INT_MAX;
		$maxSeconds = $consumer->getMaxSeconds() ?: PHP_INT_MAX;

		while (microtime(true) < $startTime + $maxSeconds && $this->messages < $maxMessages) {
			$channel->getClient()->run($consumer->getTickSeconds() ?: $maxSeconds);

			if ($consumer instanceof BunnyTickingConsumer) {
				$consumer->tick($channel);
			}
		}

		$channel->getClient()->disconnect();
	}

	public function handleMessage(BunnyConsumerInterface $consumer, Message $message, Channel $channel, Client $client): void
	{
		$data = $message->content;

		switch ((string) $message->getHeader("content-type")) {
			case ContentTypes::APPLICATION_JSON:
				$object = $this->serializer->deserialize($data, $consumer->getMessageClassName(), 'json');

				break;
			case ContentTypes::APPLICATION_PROTOBUF:
				$object = $this->serializer->deserialize($data, $consumer->getMessageClassName(), ':protobuf');

				break;
			default:
				throw new BunnyException("Message does not have 'content-type' header, cannot deserialize data.");
		}

		$consumer->handleMessage($object, $message, $channel, $client);
		$maxMessages = $consumer->getMaxMessages();
		$this->messages++;

		if ($maxMessages !== null && $this->messages >= $maxMessages) {
			$client->stop();
		}
	}
}
