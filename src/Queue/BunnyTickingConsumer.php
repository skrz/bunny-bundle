<?php

declare(strict_types=1);

namespace Skrz\Bundle\BunnyBundle\Queue;

use Bunny\Channel;
use LogicException;

/** @author Lukas Senfeld <skrz@senfeld.net> */
abstract class BunnyTickingConsumer extends BunnyConsumer implements BunnyTickingConsumerInterface
{

	protected ?float $tickSeconds = null;

	public function getTickSeconds(): ?float
	{
		return $this->tickSeconds;
	}

	public function setTickSeconds(?float $tickSeconds): BunnyConsumer
	{
		$this->tickSeconds = $tickSeconds;

		return $this;
	}

	public function tick(Channel $channel): void
	{
		throw new LogicException('You must override the tick() method in the concrete BunnyTickingConsumer class.');
	}
}
