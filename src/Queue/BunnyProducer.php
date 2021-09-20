<?php

declare(strict_types=1);

namespace Skrz\Bundle\BunnyBundle\Queue;

use Bunny\Channel;
use ReflectionClass;
use Skrz\Bundle\BunnyBundle\Exception\BunnyException;
use Skrz\Bundle\BunnyBundle\Service\BunnyManager;
use Skrz\Bundle\BunnyBundle\Service\ContentTypes;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Lukas Senfeld <skrz@senfeld.net>
 */
abstract class BunnyProducer implements BunnyProducerInterface
{

	private const NAME_SUFFIX_TO_REMOVE = "Producer";
	private BunnyManager $manager;
	private SerializerInterface $serializer;
	private bool $autoCommit = false;
	private string $exchange = "";
	private bool $immediate = false;
	private bool $mandatory = false;
	private string $contentType = ContentTypes::APPLICATION_JSON;
	private string $messageClassName;
	private string $name;
	private string $routingKey = "";
	private bool $transactional = false;

	public function __construct(?string $name = null)
	{
		$this->name = $name ?? $this->generateName();
		$this->configure();
	}

	public function inject(BunnyManager $manager, SerializerInterface $serializer): void
	{
		$this->manager = $manager;
		$this->serializer = $serializer;
	}

	/**
	 * @param string|object $message
	 * @param array<string,string> $headers
	 */
	public function publish($message, ?string $routingKey = null, array $headers = []): void
	{
		if (is_string($message)) {
			$message = $this->serializer->deserialize($message, $this->messageClassName, 'json');
		}

		if ($this instanceof BunnyPreProcessorInterface) {
			$this->preProcessMessage($message, $this->manager->getChannel());
		}

		switch ($this->contentType) {
			case ContentTypes::APPLICATION_JSON:
				$message = $this->serializer->serialize($message, 'json');

				break;
			case ContentTypes::APPLICATION_PROTOBUF:
				$message = $this->serializer->serialize($message, 'protobuf');

				break;
			default:
				throw new BunnyException("Unhandled content type '{$this->contentType}'.");
		}

		$headers["content-type"] = $this->contentType;

		if ($this->isTransactional()) {
			$channel = $this->manager->getTransactionalChannel();
		} else {
			$channel = $this->manager->getChannel();
		}

		if (!$channel instanceof Channel) {
			throw new BunnyException("Could not open channel.");
		}

		$channel->publish(
			$message,
			$headers,
			$this->exchange,
			$routingKey ?? $this->routingKey,
			$this->mandatory,
			$this->immediate
		);

		if ($this->isTransactional() && $this->isAutoCommit()) {
			$channel->txCommit();
		}
	}

	abstract public function configure(): void;

	private function generateName(): string
	{
		$reflection = new ReflectionClass($this);
		$name = $reflection->getShortName();

		return preg_replace('/' . self::NAME_SUFFIX_TO_REMOVE . '$/', '', $name);
	}

	public function getExchange(): string
	{
		return $this->exchange;
	}

	public function setExchange(string $exchange): BunnyProducer
	{
		$this->exchange = $exchange;

		return $this;
	}

	public function getMessageClassName(): string
	{
		return $this->messageClassName;
	}

	public function setMessageClassName(string $messageClassName): BunnyProducer
	{
		$this->messageClassName = $messageClassName;

		return $this;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): BunnyProducer
	{
		$this->name = $name;

		return $this;
	}

	public function getRoutingKey(): string
	{
		return $this->routingKey;
	}

	public function setRoutingKey(string $routingKey): BunnyProducer
	{
		$this->routingKey = $routingKey;

		return $this;
	}

	public function isAutoCommit(): bool
	{
		return $this->autoCommit;
	}

	public function setAutoCommit(bool $autoCommit): BunnyProducer
	{
		$this->autoCommit = $autoCommit;

		return $this;
	}

	public function isImmediate(): bool
	{
		return $this->immediate;
	}

	public function setImmediate(bool $immediate): BunnyProducer
	{
		$this->immediate = $immediate;

		return $this;
	}

	public function isMandatory(): bool
	{
		return $this->mandatory;
	}

	public function setMandatory(bool $mandatory): BunnyProducer
	{
		$this->mandatory = $mandatory;

		return $this;
	}

	public function isTransactional(): bool
	{
		return $this->transactional;
	}

	public function setTransactional(bool $transactional): BunnyProducer
	{
		$this->transactional = $transactional;

		return $this;
	}

	public function getContentType(): string
	{
		return $this->contentType;
	}

	public function setContentType(string $contentType): BunnyProducer
	{
		$this->contentType = $contentType;

		return $this;
	}
}
