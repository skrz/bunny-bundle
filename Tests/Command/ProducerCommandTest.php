<?php
namespace Skrz\Bundle\BunnyBundle\Tests\Command;

use Bunny\Client;
use Bunny\Message as BunnyMessage;
use Skrz\Bundle\BunnyBundle\ContentTypes;
use Skrz\Bundle\BunnyBundle\SkrzBunnyBundle;
use Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message;
use Skrz\Bundle\BunnyBundle\Tests\Fixtures\Meta\MessageMeta;
use Skrz\Bundle\BunnyBundle\Tests\Fixtures\TestKernel;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ProducerCommandTest extends \PHPUnit_Framework_TestCase
{

	private function runWithConfig($config, $input)
	{
		$kernel = new TestKernel($config);
		$kernel->boot();
		/** @var SkrzBunnyBundle $bundle */
		$bundle = $kernel->getBundle("SkrzBunnyBundle");
		$application = new Application($kernel);
		$bundle->registerCommands($application);
		$tester = new CommandTester($application->find("bunny:producer"));
		$tester->execute($input);

		return $kernel->getContainer();
	}

	protected function setUp()
	{
		$client = new Client();
		$client->connect();

		$channel = $client->channel();

		$channel->queueDelete("producer_test_queue");
		$channel->exchangeDelete("producer_test_exchange");

		$client->disconnect();
	}

	protected function tearDown()
	{
		$this->setUp();
	}

	public function testProducerWithJson()
	{
		$this->runWithConfig(__DIR__ . "/../Fixtures/producer.yml", [
			"producer-name" => "JsonMessage",
			"message" => MessageMeta::toJson(
				(new Message())
					->setIntValue(234)
					->setFloatValue(2.41)
					->setStringValue("test")
			)
		]);

		$client = new Client();
		$client->connect();
		$channel = $client->channel();

		/** @var BunnyMessage $msg */
		$msg = $channel->get("producer_test_queue", true);
		$this->assertNotNull($msg);

		$this->assertEquals(ContentTypes::APPLICATION_JSON, $msg->getHeader("content-type"));
		$object = MessageMeta::fromJson($msg->content);
		$this->assertNotNull($object);
		$this->assertEquals(234, $object->getIntValue());
		$this->assertEquals(2.41, $object->getFloatValue());
		$this->assertEquals("test", $object->getStringValue());
	}

	public function testProducerWithProtobuf()
	{
		$this->runWithConfig(__DIR__ . "/../Fixtures/producer.yml", [
			"producer-name" => "ProtobufMessage",
			"message" => MessageMeta::toJson(
				(new Message())
					->setIntValue(234)
					->setFloatValue(2.41)
					->setStringValue("test")
			)
		]);

		$client = new Client();
		$client->connect();
		$channel = $client->channel();

		/** @var BunnyMessage $msg */
		$msg = $channel->get("producer_test_queue", true);
		$this->assertNotNull($msg);

		$this->assertEquals(ContentTypes::APPLICATION_PROTOBUF, $msg->getHeader("content-type"));
		$object = MessageMeta::fromProtobuf($msg->content);
		$this->assertNotNull($object);
		$this->assertEquals(234, $object->getIntValue());
		$this->assertEquals(2.41, $object->getFloatValue());
		$this->assertEquals("test", $object->getStringValue());
	}

	public function testEmptyExchangeProducer()
	{
		$this->runWithConfig(__DIR__ . "/../Fixtures/producer.yml", [
			"producer-name" => "EmptyExchange",
			"message" => MessageMeta::toJson(
				(new Message())
					->setIntValue(234)
					->setFloatValue(2.41)
					->setStringValue("test")
			)
		]);

		$client = new Client();
		$client->connect();
		$channel = $client->channel();

		/** @var BunnyMessage $msg */
		$msg = $channel->get("producer_test_queue", true);
		$this->assertNotNull($msg);

		$this->assertEquals(ContentTypes::APPLICATION_JSON, $msg->getHeader("content-type"));
		$object = MessageMeta::fromJson($msg->content);
		$this->assertNotNull($object);
		$this->assertEquals(234, $object->getIntValue());
		$this->assertEquals(2.41, $object->getFloatValue());
		$this->assertEquals("test", $object->getStringValue());
	}

}
