<?php

declare(strict_types=1);

namespace Skrz\Bundle\BunnyBundle\Queue;

use ReflectionClass;

/** @author Lukas Senfeld <skrz@senfeld.net> */
abstract class BunnyConsumer implements BunnyConsumerInterface
{

	private const NAME_SUFFIX_TO_REMOVE = "Consumer";
	private string $name;
	private string $messageClassName;
	protected ?string $exchange = null;
	protected string $routingKey = "";
	protected ?string $queue = null;
	protected string $consumerTag = "";
	protected bool $noLocal = false;
	protected bool $noAck = false;
	protected bool $exclusive = false;
	protected bool $nowait = false;
	protected ?int $prefetchCount = null;
	protected ?int $prefetchSize = null;
	protected ?int $maxMessages = null;
	protected ?float $maxSeconds = null;
	protected array $arguments = [];

	public function __construct(?string $name = null)
	{
		$this->name = $name ?? $this->generateName();
		$this->configure();
	}

	private function generateName(): string
	{
		$reflection = new ReflectionClass($this);
		$name = $reflection->getShortName();

		return preg_replace('/' . self::NAME_SUFFIX_TO_REMOVE . '$/', '', $name);
	}

	abstract public function configure(): void;

	public function getName(): string
	{
		return $this->name;
	}

	public function getExchange(): ?string
	{
		return $this->exchange;
	}

	public function setExchange(?string $exchange): BunnyConsumer
	{
		$this->exchange = $exchange;

		return $this;
	}

	public function getRoutingKey(): string
	{
		return $this->routingKey;
	}

	public function setRoutingKey(string $routingKey): BunnyConsumer
	{
		$this->routingKey = $routingKey;

		return $this;
	}

	public function getQueue(): ?string
	{
		return $this->queue;
	}

	public function setQueue(?string $queue): BunnyConsumer
	{
		$this->queue = $queue;

		return $this;
	}

	public function getConsumerTag(): string
	{
		return $this->consumerTag;
	}

	public function setConsumerTag(string $consumerTag): BunnyConsumer
	{
		$this->consumerTag = $consumerTag;

		return $this;
	}

	public function isNoLocal(): bool
	{
		return $this->noLocal;
	}

	public function setNoLocal(bool $noLocal): BunnyConsumer
	{
		$this->noLocal = $noLocal;

		return $this;
	}

	public function getArguments(): array
	{
		return $this->arguments;
	}

	public function setArguments(array $arguments): BunnyConsumer
	{
		$this->arguments = $arguments;

		return $this;
	}

	public function isNoAck(): bool
	{
		return $this->noAck;
	}

	public function setNoAck(bool $noAck): BunnyConsumer
	{
		$this->noAck = $noAck;

		return $this;
	}

	public function isExclusive(): bool
	{
		return $this->exclusive;
	}

	public function setExclusive(bool $exclusive): BunnyConsumer
	{
		$this->exclusive = $exclusive;

		return $this;
	}

	public function isNowait(): bool
	{
		return $this->nowait;
	}

	public function setNowait(bool $nowait): BunnyConsumer
	{
		$this->nowait = $nowait;

		return $this;
	}

	public function getPrefetchCount(): ?int
	{
		return $this->prefetchCount;
	}

	public function setPrefetchCount(?int $prefetchCount): BunnyConsumer
	{
		$this->prefetchCount = $prefetchCount;

		return $this;
	}

	public function getPrefetchSize(): ?int
	{
		return $this->prefetchSize;
	}

	public function setPrefetchSize(?int $prefetchSize): BunnyConsumer
	{
		$this->prefetchSize = $prefetchSize;

		return $this;
	}

	public function getMaxMessages(): ?int
	{
		return $this->maxMessages;
	}

	public function setMaxMessages(?int $maxMessages): BunnyConsumer
	{
		$this->maxMessages = $maxMessages;

		return $this;
	}

	public function getMaxSeconds(): ?float
	{
		return $this->maxSeconds;
	}

	public function setMaxSeconds(?float $maxSeconds): BunnyConsumer
	{
		$this->maxSeconds = $maxSeconds;

		return $this;
	}

	public function getMessageClassName(): string
	{
		return $this->messageClassName;
	}

	public function setMessageClassName(string $messageClassName): BunnyConsumer
	{
		$this->messageClassName = $messageClassName;

		return $this;
	}
}
