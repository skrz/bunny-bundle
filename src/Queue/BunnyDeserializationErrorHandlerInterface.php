<?php

namespace Skrz\Bundle\BunnyBundle\Queue;

use Bunny\Channel;
use Bunny\Client;
use Bunny\Message;
use Throwable;

interface BunnyDeserializationErrorHandlerInterface
{

	public function handleDeserializationError(Throwable $e, Message $message, Channel $channel, Client $client): void;
}