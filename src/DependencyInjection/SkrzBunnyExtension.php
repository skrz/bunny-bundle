<?php

declare(strict_types=1);

namespace Skrz\Bundle\BunnyBundle\DependencyInjection;

use Skrz\Bundle\BunnyBundle\Queue\BunnyConsumerInterface;
use Skrz\Bundle\BunnyBundle\Queue\BunnyProducerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/** @author Lukas Senfeld <skrz@senfeld.net> */
class SkrzBunnyExtension extends Extension
{

	public const BUNNY_CONSUMER_TAG = 'skrz.bunny.consumer';
	public const BUNNY_PRODUCER_TAG = 'skrz.bunny.producer';

	/**
	 * @param string[] $configs
	 * @throws \Exception
	 */
	public function load(array $configs, ContainerBuilder $container): void
	{
		$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
		$loader->load('services.yaml');

		$container->setParameter('skrz_bunny', $this->processConfiguration(new Configuration(), $configs));

		$container->registerForAutoconfiguration(BunnyConsumerInterface::class)
			->addTag(self::BUNNY_CONSUMER_TAG);
		$container->registerForAutoconfiguration(BunnyProducerInterface::class)
			->addTag(self::BUNNY_PRODUCER_TAG);
	}
}
