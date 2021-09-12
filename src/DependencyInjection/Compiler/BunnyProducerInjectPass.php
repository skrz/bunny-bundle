<?php

declare(strict_types=1);

namespace Skrz\Bundle\BunnyBundle\DependencyInjection\Compiler;

use Skrz\Bundle\BunnyBundle\DependencyInjection\SkrzBunnyExtension;
use Skrz\Bundle\BunnyBundle\Service\BunnyManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Serializer\SerializerInterface;

class BunnyProducerInjectPass implements CompilerPassInterface
{

	public function process(ContainerBuilder $container): void
	{
		$manager = new Reference(BunnyManager::class);
		$serializer = new Reference(SerializerInterface::class);
		$serviceIds = array_keys($container->findTaggedServiceIds(SkrzBunnyExtension::BUNNY_PRODUCER_TAG));

		foreach ($serviceIds as $serviceId) {
			$configurationDef = $container->getDefinition($serviceId);
			$methodCalls = $configurationDef->getMethodCalls();

			if (isset($methodCalls["inject"])) {
				continue;
			}

			$methodCalls[] = ["inject", [$manager, $serializer]];
			$configurationDef->setMethodCalls($methodCalls);
			$container->setDefinition($serviceId, $configurationDef);
		}
	}
}
