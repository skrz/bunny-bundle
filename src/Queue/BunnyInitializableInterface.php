<?php

namespace Skrz\Bundle\BunnyBundle\Queue;

use Bunny\Channel;

interface BunnyInitializableInterface
{

	public function initialize(Channel $channel, string ...$argv): void;

}
