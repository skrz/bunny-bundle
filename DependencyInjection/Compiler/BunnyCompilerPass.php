<?php
namespace Skrz\Bundle\BunnyBundle\DependencyInjection\Compiler;

use Doctrine\Common\Annotations\AnnotationReader;
use Skrz\Bundle\BunnyBundle\Annotation\Consumer;
use Skrz\Bundle\BunnyBundle\Annotation\Producer;
use Skrz\Bundle\BunnyBundle\BunnyException;
use Skrz\Bundle\BunnyBundle\ContentTypes;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class BunnyCompilerPass implements CompilerPassInterface
{

	/** @var string */
	private $configKey;

	/** @var string */
	private $clientServiceId;

	/** @var string */
	private $managerServiceId;

	/** @var string */
	private $channelServiceId;

	/** @var string */
	private $setupCommandServiceId;

	/** @var string */
	private $consumerCommandServiceId;

	/** @var string */
	private $producerCommandServiceId;

	/** @var AnnotationReader */
	private $annotationReader;

	public function __construct(
		$configKey,
		$clientServiceId,
		$managerServiceId,
		$channelServiceId,
		$setupCommandServiceId,
		$consumerCommandServiceId,
		$producerCommandServiceId,
		AnnotationReader $annotationReader
	) {
		$this->configKey = $configKey;
		$this->clientServiceId = $clientServiceId;
		$this->managerServiceId = $managerServiceId;
		$this->channelServiceId = $channelServiceId;
		$this->setupCommandServiceId = $setupCommandServiceId;
		$this->consumerCommandServiceId = $consumerCommandServiceId;
		$this->producerCommandServiceId = $producerCommandServiceId;
		$this->annotationReader = $annotationReader;
	}


	public function process(ContainerBuilder $container)
	{
		if (!$container->hasParameter($this->configKey)) {
			throw new \InvalidArgumentException("Container doesn't have parameter '{$this->configKey}', SkrzBunnyExtension probably haven't processed config.");
		}

		$config = $container->getParameter($this->configKey);

		$parameterBag = $container->getParameterBag();

		$consumers = [];
		$producers = [];
		foreach ($container->getDefinitions() as $serviceId => $definition) {
			if ($definition->isAbstract() ||
				!$definition->isPublic() ||
				!$definition->getClass()
			) {
				continue;
			}

			$className = $parameterBag->resolveValue($definition->getClass());

			if (!class_exists($className)) {
				continue;
			}

			$rc = new \ReflectionClass($className);

			if (strpos($rc->getDocComment(), "@Consumer") === false && strpos($rc->getDocComment(), "@Producer") === false) {
				continue;
			}

			foreach ($this->annotationReader->getClassAnnotations($rc) as $annotation) {
				if ($annotation instanceof Consumer) {
					if (empty($annotation->queue) === empty($annotation->exchange)) {
						throw new BunnyException(
							"Either 'queue', or 'exchange' (but not both) has to be specified {$className} (service: {$serviceId})."
						);
					}

					$annotation->name = $serviceId;
					$annotation->className = $className;

					$consumerName = $rc->getShortName();
					if (substr($consumerName, -8 /* -strlen("Consumer") */) === "Consumer") {
						$consumerName = substr($consumerName, 0, -8);
					}
					$consumerName = strtolower($consumerName);

					if (isset($consumers[$consumerName]) && $consumers[$consumerName][0]["className"] !== $className) {
						throw new BunnyException(
							"Multiple consumer services would result in same name: " .
							"{$consumers[$consumerName][0]["name"]} ({$consumers[$consumerName][0]["className"]}) " .
							"and {$serviceId} ({$className})."
						);

					} elseif (!isset($consumers[$consumerName])) {
						$consumers[$consumerName] = [];
					}

					$consumers[$consumerName][] = (array)$annotation;

				} elseif ($annotation instanceof Producer) {
					$annotation->name = $serviceId;
					$annotation->className = $className;

					$producerName = $rc->getShortName();
					if (substr($producerName, -8 /* -strlen("Producer") */) === "Producer") {
						$producerName = substr($producerName, 0, -8);
					}
					$producerName = strtolower($producerName);

					if (isset($producers[$producerName])) {
						throw new BunnyException(
							"Multiple producer services would result in same name: " .
							"{$producers[$producerName]["name"]} ({$producers[$producerName]["className"]}) " .
							"and {$serviceId} ({$className})."
						);
					}

					if (empty($annotation->contentType)) {
						$annotation->contentType = ContentTypes::APPLICATION_JSON;
					}

					$producers[$producerName] = (array)$annotation;

					$definition->setArguments([
						$annotation->exchange,
						$annotation->routingKey,
						$annotation->mandatory,
						$annotation->immediate,
						$annotation->meta,
						$annotation->beforeMethod,
						$annotation->contentType,
						new Reference($this->managerServiceId),
					]);
				}
			}
		}

		$client = new Definition("Bunny\\Client", [[
			"host" => $config["host"],
			"port" => $config["port"],
			"vhost" => $config["vhost"],
			"user" => $config["user"],
			"password" => $config["password"],
			"heartbeat" => $config["heartbeat"],
		]]);
		$client->setPublic(true);
		$container->setDefinition($this->clientServiceId, $client);

		$bunnyManager = new Definition("Skrz\\Bundle\\BunnyBundle\\BunnyManager", [
			new Reference("service_container"),
			$this->clientServiceId,
			$config,
		]);
		$bunnyManager->setPublic(true);
		$container->setDefinition($this->managerServiceId, $bunnyManager);

		$channel = new Definition("Bunny\\Channel");
		$channel->setFactory([new Reference($this->managerServiceId), "getChannel"]);
		$channel->setPublic(true);
		$container->setDefinition($this->channelServiceId, $channel);

		$setupCommand = new Definition("Skrz\\Bundle\\BunnyBundle\\Command\\SetupCommand", [
			new Reference($this->managerServiceId),
		]);
		$setupCommand->setPublic(true);
		$container->setDefinition($this->setupCommandServiceId, );

		$consumerCommand = new Definition("Skrz\\Bundle\\BunnyBundle\\Command\\ConsumerCommand", [
			new Reference("service_container"),
			new Reference($this->managerServiceId),
			$consumers,
		]);
		$consumerCommand->setPublic(true);
		$container->setDefinition($this->consumerCommandServiceId, $consumerCommand);

		$producerCommand = new Definition("Skrz\\Bundle\\BunnyBundle\\Command\\ProducerCommand", [
			new Reference("service_container"),
			new Reference($this->managerServiceId),
			$producers,
		]);
		$producerCommand->setPublic(true);
		$container->setDefinition($this->producerCommandServiceId, $producerCommand);
	}

}
