<?php
namespace Skrz\Bundle\BunnyBundle\Tests\Fixtures;

use Bunny\Channel;
use Skrz\Bundle\BunnyBundle\Annotation\Consumer;
use Bunny\Message as BunnyMessage;

/**
 * @author Jakub Kulhan <jakub.kulhan@gmail.com>
 *
 * @Consumer(
 *     queue="consumer_test_queue",
 *     meta="Skrz\Bundle\BunnyBundle\Tests\Fixtures\Meta\MessageMeta",
 *     maxSeconds=1.0
 * )
 */
class TestConsumer
{

	/** @var Message */
	public $message;

	public function handleMessage(Message $message, BunnyMessage $bunnyMessage, Channel $channel)
	{
		$this->message = $message;
		$channel->ack($bunnyMessage);
	}

}
