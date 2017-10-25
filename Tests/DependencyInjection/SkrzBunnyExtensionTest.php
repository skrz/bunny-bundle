<?php

namespace Skrz\Bundle\BunnyBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Skrz\Bundle\BunnyBundle\DependencyInjection\SkrzBunnyExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class SkrzBunnyExtensionTest extends TestCase
{
	public function getAlias()
	{
		return "bunny";
	}

	public function testMultipleConfigsQueuesExchanges()
	{
		$container = $this->getContainer();
		$extension = new SkrzBunnyExtension();

		$queue1 = 'queue1';
		$exchange1 = 'exchange1';
		$exchange2 = 'exchange2';
		$queue2 = 'queue2';

		$firstQueueConfig = [
			'queues' => [
				$queue1 => [],
			],
			'exchanges' => [
				$exchange1 => [],
			],
		];

		$secondQueueConfig = [
			'queues' => [
				$queue2 => [],
			],
			'exchanges' => [
				$exchange2 => [],
			],
		];
		$extension->load([$firstQueueConfig, $secondQueueConfig], $container);

		$bunnyConfig = $container->getParameter('bunny');
		$queuesConfig = $bunnyConfig['queues'];
		$exchangesConfig = $bunnyConfig['exchanges'];

		$this->assertCount(2, $queuesConfig);
		$this->assertCount(2, $exchangesConfig);

		$this->assertArrayHasKey($queue1, $queuesConfig);
		$this->assertArrayHasKey($queue2, $queuesConfig);
		$this->assertArrayHasKey($exchange1, $exchangesConfig);
		$this->assertArrayHasKey($exchange2, $exchangesConfig);
	}

	private function getContainer()
	{
		return new ContainerBuilder(
			new ParameterBag(
				[
					'kernel.name' => 'app',
					'kernel.debug' => false,
					'kernel.cache_dir' => sys_get_temp_dir(),
					'kernel.environment' => 'test',
					'kernel.root_dir' => __DIR__ . '/../../',
				]
			)
		);
	}
}
