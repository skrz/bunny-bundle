<?php

declare(strict_types=1);

namespace Skrz\Bundle\BunnyBundle\Queue;

use Bunny\Channel;

/** @author Lukas Senfeld <skrz@senfeld.net> */
interface BunnyTickingConsumerInterface extends BunnyConsumerInterface
{
	public function tick(Channel $channel): void;
}
