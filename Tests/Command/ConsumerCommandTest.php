<?php
namespace Skrz\Bundle\BunnyBundle\Tests\Command;

use Bunny\Client;
use Skrz\Bundle\BunnyBundle\ContentTypes;
use Skrz\Bundle\BunnyBundle\SkrzBunnyBundle;
use Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message;
use Skrz\Bundle\BunnyBundle\Tests\Fixtures\Meta\MessageMeta;
use Skrz\Bundle\BunnyBundle\Tests\Fixtures\TestConsumer;
use Skrz\Bundle\BunnyBundle\Tests\Fixtures\TestKernel;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ConsumerCommandTest extends \PHPUnit_Framework_TestCase
{

	private function runWithConfig($config, $input)
	{
		$kernel = new TestKernel($config);
		$kernel->boot();
		/** @var SkrzBunnyBundle $bundle */
		$bundle = $kernel->getBundle("SkrzBunnyBundle");
		$application = new Application($kernel);
		$bundle->registerCommands($application);
		$tester = new CommandTester($application->find("bunny:consumer"));
		$tester->execute($input);

		return $kernel->getContainer();
	}

	protected function setUp()
	{
		$client = new Client();
		$client->connect();

		$channel = $client->channel();

		$channel->queueDelete("consumer_test_queue");
		$channel->exchangeDelete("consumer_test_exchange");

		$client->disconnect();
	}

	protected function tearDown()
	{
		$this->setUp();
	}

	public function testConsumerWithJson()
	{
		$container = $this->runWithConfig(__DIR__ . "/../Fixtures/consumer.yml", ["consumer-name" => "Test"]);
		/** @var TestConsumer $consumer */
		$consumer = $container->get("consumer.test");

		$this->assertNull($consumer->message);

		$client = new Client();
		$client->connect();
		$channel = $client->channel();

		$message = new Message();
		$message->setIntValue(42);
		$message->setFloatValue(3.14);
		$message->setStringValue("hello, world");

		$channel->publish(MessageMeta::toJson($message), ["content-type" => ContentTypes::APPLICATION_JSON], "consumer_test_exchange", "test.message");

		$client->disconnect();

		$container = $this->runWithConfig(__DIR__ . "/../Fixtures/consumer.yml", ["consumer-name" => "Test"]);
		/** @var TestConsumer $consumer */
		$consumer = $container->get("consumer.test");

		$this->assertNotNull($consumer->message);
		$this->assertEquals(42, $consumer->message->getIntValue());
		$this->assertEquals(3.14, $consumer->message->getFloatValue());
		$this->assertEquals("hello, world", $consumer->message->getStringValue());
	}

	public function testConsumerWithProtobuf()
	{
		$container = $this->runWithConfig(__DIR__ . "/../Fixtures/consumer.yml", ["consumer-name" => "Test"]);
		/** @var TestConsumer $consumer */
		$consumer = $container->get("consumer.test");

		$this->assertNull($consumer->message);

		$client = new Client();
		$client->connect();
		$channel = $client->channel();

		$message = new Message();
		$message->setIntValue(42);
		$message->setFloatValue(3.14);
		$message->setStringValue("hello, world");

		$channel->publish(MessageMeta::toProtobuf($message), ["content-type" => ContentTypes::APPLICATION_PROTOBUF], "consumer_test_exchange", "test.message");

		$client->disconnect();

		$container = $this->runWithConfig(__DIR__ . "/../Fixtures/consumer.yml", ["consumer-name" => "Test"]);
		/** @var TestConsumer $consumer */
		$consumer = $container->get("consumer.test");

		$this->assertNotNull($consumer->message);
		$this->assertEquals(42, $consumer->message->getIntValue());
		$this->assertEquals(3.14, $consumer->message->getFloatValue());
		$this->assertEquals("hello, world", $consumer->message->getStringValue());
	}

}
