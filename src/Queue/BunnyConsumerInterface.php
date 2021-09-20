<?php

declare(strict_types=1);

namespace Skrz\Bundle\BunnyBundle\Queue;

use Bunny\Channel;
use Bunny\Client;
use Bunny\Message;

/**
 * @author Lukas Senfeld <skrz@senfeld.net>
 * @method handleMessage(object $object, Message $message, Channel $channel, Client $client)
 */
interface BunnyConsumerInterface
{
	public function configure(): void;
	public function getName(): string;
	public function getExchange(): string;
	public function setExchange(string $exchange): BunnyConsumerInterface;
	public function getRoutingKey(): string;
	public function setRoutingKey(string $routingKey): BunnyConsumerInterface;
	public function getQueue(): ?string;
	public function setQueue(?string $queue): BunnyConsumerInterface;
	public function getConsumerTag(): string;
	public function setConsumerTag(string $consumerTag): BunnyConsumerInterface;
	public function isNoLocal(): bool;
	public function setNoLocal(bool $noLocal): BunnyConsumerInterface;
	public function isNoAck(): bool;
	public function setNoAck(bool $noAck): BunnyConsumerInterface;
	public function isExclusive(): bool;
	public function setExclusive(bool $exclusive): BunnyConsumerInterface;
	public function isNowait(): bool;
	public function setNowait(bool $nowait): BunnyConsumerInterface;

	/** @return string[] */
	public function getArguments(): array;

	/** @param string[] $arguments */
	public function setArguments(array $arguments): BunnyConsumerInterface;
	public function getPrefetchCount(): ?int;
	public function setPrefetchCount(?int $prefetchCount): BunnyConsumerInterface;
	public function getPrefetchSize(): ?int;
	public function setPrefetchSize(?int $prefetchSize): BunnyConsumerInterface;
	public function getMaxMessages(): ?int;
	public function setMaxMessages(?int $maxMessages): BunnyConsumerInterface;
	public function getMaxSeconds(): ?float;
	public function setMaxSeconds(?float $maxSeconds): BunnyConsumerInterface;
	public function getMessageClassName(): string;
	public function setMessageClassName(string $messageClassName): BunnyConsumerInterface;
}
