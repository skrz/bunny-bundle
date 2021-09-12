<?php

declare(strict_types=1);

namespace Skrz\Bundle\BunnyBundle\Queue;

use Bunny\Channel;
use LogicException;

/** @author Lukas Senfeld <skrz@senfeld.net> */
abstract class BunnyTickingConsumer extends BunnyConsumer
{

	protected ?float $tickSeconds = null;

	public function tick(Channel $channel): void
	{
		throw new LogicException('You must override the tick() method in the concrete BunnyTickingConsumer class.');
	}
}
