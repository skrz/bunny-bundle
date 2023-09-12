<?php

declare(strict_types=1);

namespace Skrz\Bundle\BunnyBundle;

use Skrz\Bundle\BunnyBundle\DependencyInjection\Compiler\BunnyProducerInjectPass;
use Skrz\Bundle\BunnyBundle\DependencyInjection\SkrzBunnyExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/** @author Lukas Senfeld <skrz@senfeld.net> */
final class SkrzBunnyBundle extends Bundle
{
	public function build(ContainerBuilder $container): void
	{
		parent::build($container);
		$container->addCompilerPass(new BunnyProducerInjectPass());
	}

	public function getContainerExtension(): SkrzBunnyExtension
	{
		return new SkrzBunnyExtension();
	}
}
