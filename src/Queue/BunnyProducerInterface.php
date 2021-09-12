<?php

declare(strict_types=1);

namespace Skrz\Bundle\BunnyBundle\Queue;

interface BunnyProducerInterface
{

	public function configure(): void;
	public function getExchange(): string;
	public function setExchange(string $exchange): BunnyProducerInterface;
	public function getMessageClassName(): string;
	public function setMessageClassName(string $messageClassName): BunnyProducerInterface;
	public function getName(): string;
	public function setName(string $name): BunnyProducerInterface;
	public function getRoutingKey(): string;
	public function setRoutingKey(string $routingKey): BunnyProducerInterface;
	public function isAutoCommit(): bool;
	public function setAutoCommit(bool $autoCommit): BunnyProducerInterface;
	public function isImmediate(): bool;
	public function setImmediate(bool $immediate): BunnyProducerInterface;
	public function isMandatory(): bool;
	public function setMandatory(bool $mandatory): BunnyProducerInterface;
	public function isTransactional(): bool;
	public function setTransactional(bool $transactional): BunnyProducerInterface;
	public function getContentType(): string;
	public function setContentType(string $contentType): BunnyProducerInterface;

	/**
	 * @param object $message
	 * @param array<string, string> $headers
	 */
	public function publish($message, ?string $routingKey = null, array $headers = []): void;
}
