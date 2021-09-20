<?php

declare(strict_types=1);

namespace Skrz\Bundle\BunnyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\Kernel;

class Configuration implements ConfigurationInterface
{
	public function getConfigTreeBuilder(): TreeBuilder
	{
		$treeBuilder = new TreeBuilder('skrz_bunny');

		if (Kernel::VERSION_ID < 40430) {
			$rootNode = $treeBuilder->root('skrz_bunny');
		} else {
			$rootNode = $treeBuilder->getRootNode();
		}

		assert($rootNode instanceof ArrayNodeDefinition);

		$rootNode->children()->scalarNode('host')->defaultValue('127.0.0.1');
		$rootNode->children()->scalarNode('port')->defaultValue(5672);
		$rootNode->children()->scalarNode('vhost')->defaultValue('/');
		$rootNode->children()->scalarNode('user')->defaultValue('guest');
		$rootNode->children()->scalarNode('password')->defaultValue('guest');
		$rootNode->children()->scalarNode('heartbeat')->defaultValue(60);

		$exchangesNode = $rootNode->children()->arrayNode('exchanges')->defaultValue([])->prototype('array');
		assert($exchangesNode instanceof ArrayNodeDefinition);
		$exchangesNode->children()->scalarNode('type');
		$exchangesNode->children()->booleanNode('durable')->defaultValue(false);
		$exchangesNode->children()->booleanNode('auto_delete')->defaultValue(false);
		$exchangesNode->children()->booleanNode('internal')->defaultValue(false);
		$exchangesNode->children()->arrayNode('arguments')->prototype('scalar')->defaultValue([]);

		$exchangesBindingsNode = $exchangesNode->children()->arrayNode('bindings')->defaultValue([])->prototype(
			'array'
		);
		assert($exchangesBindingsNode instanceof ArrayNodeDefinition);
		$exchangesBindingsNode->children()->scalarNode('exchange')->isRequired();
		$exchangesBindingsNode->children()->scalarNode('routing_key')->defaultValue('');
		$exchangesBindingsNode->children()->arrayNode('arguments')->prototype('scalar')->defaultValue([]);

		$queuesNode = $rootNode->children()->arrayNode('queues')->defaultValue([])->prototype('array');
		assert($queuesNode instanceof ArrayNodeDefinition);
		$queuesNode->children()->booleanNode('durable')->defaultValue(false);
		$queuesNode->children()->booleanNode('exclusive')->defaultValue(false);
		$queuesNode->children()->booleanNode('auto_delete')->defaultValue(false);
		$queuesNode->children()->arrayNode('arguments')->prototype('scalar')->defaultValue([]);

		$queuesBindingsNode = $queuesNode->children()->arrayNode('bindings')->defaultValue([])->prototype('array');
		assert($queuesBindingsNode instanceof ArrayNodeDefinition);
		$queuesBindingsNode->children()->scalarNode('exchange')->isRequired();
		$queuesBindingsNode->children()->scalarNode('routing_key')->defaultValue('');
		$queuesBindingsNode->children()->arrayNode('arguments')->prototype('scalar')->defaultValue([]);

		return $treeBuilder;
	}
}