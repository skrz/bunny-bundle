<?php

namespace Skrz\Bundle\BunnyBundle\Queue;

use Bunny\Channel;

/**
 * @author Lukas Senfeld <skrz@senfeld.net>
 * method have to be declared magically otherwise $message could not use strong type hint
 * @method preProcessMessage(mixed $message, Channel $channel): void
 */
interface BunnyPreProcessorInterface
{

}
