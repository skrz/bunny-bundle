<?php
namespace Skrz\Bundle\BunnyBundle\Tests\Command;

use Bunny\Client;
use Bunny\Exception\ClientException;
use Skrz\Bundle\BunnyBundle\SkrzBunnyBundle;
use Skrz\Bundle\BunnyBundle\Tests\Fixtures\TestKernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class SetupCommandTest extends \PHPUnit_Framework_TestCase
{

	private function runWithConfig($config)
	{
		$kernel = new TestKernel($config);
		$kernel->boot();
		/** @var SkrzBunnyBundle $bundle */
		$bundle = $kernel->getBundle("SkrzBunnyBundle");
		$application = new Application($kernel);
		$bundle->registerCommands($application);
		$tester = new CommandTester($application->find("bunny:setup"));
		$tester->execute([]);
	}

	public function testWontConnect()
	{
		$this->setExpectedException("Bunny\\Exception\\ClientException");
		$this->runWithConfig(__DIR__ . "/../Fixtures/wont_connect.yml");
	}

	public function testExchange()
	{
		$client = new Client();
		$client->connect();

		try {
			$channel = $client->channel();
			$channel->exchangeDeclare("test_direct_exchange", "direct", true);
			$this->fail("Exchange should not exist.");
		} catch (ClientException $e) {
		}

		try {
			$channel = $client->channel();
			$channel->exchangeDeclare("test_topic_exchange", "direct", true);
			$this->fail("Exchange should not exist.");
		} catch (ClientException $e) {
		}

		$this->runWithConfig(__DIR__ . "/../Fixtures/exchange.yml");

		try {
			$channel = $client->channel();
			$channel->exchangeDeclare("test_direct_exchange", "direct", true);
			$channel->exchangeDelete("test_direct_exchange");
		} catch (ClientException $e) {
			$this->fail("Exchange should exist by now.");
		}

		try {
			$channel = $client->channel();
			$channel->exchangeDeclare("test_topic_exchange", "direct", true);
			$channel->exchangeDelete("test_topic_exchange");
		} catch (ClientException $e) {
			$this->fail("Exchange should exist by now.");
		}
	}

	public function testqueue()
	{
		$client = new Client();
		$client->connect();

		try {
			$channel = $client->channel();
			$channel->queueDeclare("test_queue", true, true);
			$this->fail("queue should not exist.");
		} catch (ClientException $e) {
		}

		$this->runWithConfig(__DIR__ . "/../Fixtures/queue.yml");

		try {
			$channel = $client->channel();
			$channel->queueDeclare("test_queue", true, true);
			$channel->queueDelete("test_queue");
		} catch (ClientException $e) {
			$this->fail("queue should exist by now.");
		}
	}

}
