<?php

declare(strict_types=1);

namespace Skrz\Bundle\BunnyBundle\Service;

use Bunny\Channel;
use Bunny\Client;
use Bunny\Protocol\MethodExchangeBindOkFrame;
use Bunny\Protocol\MethodExchangeDeclareOkFrame;
use Bunny\Protocol\MethodQueueBindOkFrame;
use Bunny\Protocol\MethodQueueDeclareOkFrame;
use Exception;
use Skrz\Bundle\BunnyBundle\Exception\BunnyException;

class BunnyManager
{

	private Client $client;
	private ?Channel $channel;
	private ?Channel $transactionalChannel;
	/** @var array */
	private array $config;
	private bool $setUpComplete = false;

	public function __construct(Client $client, array $config)
	{
		$this->config = $config;
		$this->client = $client;
		$this->channel = null;
	}

	public function getClient(): Client
	{
		return $this->client;
	}

	public function createChannel(): Channel
	{
		if (!$this->getClient()->isConnected()) {
			$this->getClient()->connect();
		}

		return $this->getClient()->channel();
	}

	public function getChannel(): Channel
	{
		if ($this->channel === null) {
			$this->channel = $this->createChannel();
		}

		return $this->channel;
	}

	public function getTransactionalChannel(): Channel
	{
		if (!$this->transactionalChannel) {
			$this->transactionalChannel = $this->createChannel();

			// create transactional channel from normal one
			try {
				$this->transactionalChannel->txSelect();
			} catch (Exception $e) {
				throw new BunnyException(sprintf("Cannot create transaction channel because: %s", $e->getMessage()));
			}
		}

		return $this->transactionalChannel;
	}

	public function setUp(): void
	{
		if ($this->setUpComplete) {
			return;
		}

		$channel = $this->getChannel();

		foreach ($this->config["exchanges"] as $exchangeName => $exchangeDefinition) {
			$frame = $channel->exchangeDeclare(
				$exchangeName,
				$exchangeDefinition["type"],
				false,
				$exchangeDefinition["durable"],
				$exchangeDefinition["auto_delete"],
				$exchangeDefinition["internal"],
				false,
				$exchangeDefinition["arguments"]
			);

			if (!($frame instanceof MethodExchangeDeclareOkFrame)) {
				throw new BunnyException("Could not declare exchange '{$exchangeName}'.");
			}
		}

		foreach ($this->config["exchanges"] as $exchangeName => $exchangeDefinition) {
			foreach ($exchangeDefinition["bindings"] as $binding) {
				$frame = $channel->exchangeBind(
					$exchangeName,
					$binding["exchange"],
					$binding["routing_key"],
					false,
					$binding["arguments"]
				);

				if (!($frame instanceof MethodExchangeBindOkFrame)) {
					throw new BunnyException(
						"Could not bind exchange '{$exchangeName}' to '{$binding["exchange"]}' with routing key '{$binding["routing_key"]}'."
					);
				}
			}
		}

		foreach ($this->config["queues"] as $queueName => $queueDefinition) {
			$frame = $channel->queueDeclare(
				$queueName,
				false,
				$queueDefinition["durable"],
				$queueDefinition["exclusive"],
				$queueDefinition["auto_delete"],
				false,
				$queueDefinition["arguments"]
			);

			if (!($frame instanceof MethodQueueDeclareOkFrame)) {
				throw new BunnyException("Could not declare queue '{$queueName}'.");
			}

			foreach ($queueDefinition["bindings"] as $binding) {
				$frame = $channel->queueBind(
					$queueName,
					$binding["exchange"],
					$binding["routing_key"],
					false,
					$binding["arguments"]
				);

				if (!($frame instanceof MethodQueueBindOkFrame)) {
					throw new BunnyException(
						"Could not bind queue '{$queueName}' to '{$binding["exchange"]}' with routing key '{$binding["routing_key"]}'."
					);
				}
			}
		}

		$this->setUpComplete = true;
	}
}
