<?php
namespace Skrz\Bundle\BunnyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class SkrzBunnyExtension extends Extension implements ConfigurationInterface
{

	public function getAlias()
	{
		return "bunny";
	}

	public function getConfigTreeBuilder()
	{
		$treeBuilder = new TreeBuilder();

		$rootNode = $treeBuilder->root("bunny");

		$rootNode->children()->scalarNode("host")->defaultValue("127.0.0.1");
		$rootNode->children()->scalarNode("port")->defaultValue(5672);
		$rootNode->children()->scalarNode("vhost")->defaultValue("/");
		$rootNode->children()->scalarNode("user")->defaultValue("guest");
		$rootNode->children()->scalarNode("password")->defaultValue("guest");
		$rootNode->children()->scalarNode("heartbeat")->defaultValue(60);

		/** @var ArrayNodeDefinition $exchangesNode */
		$exchangesNode = $rootNode->children()->arrayNode("exchanges")->normalizeKeys(false)->defaultValue([])->prototype("array");
		$exchangesNode->children()->scalarNode("type");
		$exchangesNode->children()->booleanNode("durable")->defaultValue(false);
		$exchangesNode->children()->booleanNode("auto_delete")->defaultValue(false);
		$exchangesNode->children()->booleanNode("internal")->defaultValue(false);
		$exchangesNode->children()->arrayNode("arguments")->normalizeKeys(false)->prototype("scalar")->defaultValue([]);

		/** @var ArrayNodeDefinition $exchangesBindingsNode */
		$exchangesBindingsNode = $exchangesNode->children()->arrayNode("bindings")->normalizeKeys(false)->defaultValue([])->prototype("array");
		$exchangesBindingsNode->children()->scalarNode("exchange")->isRequired();
		$exchangesBindingsNode->children()->scalarNode("routing_key")->defaultValue("");
		$exchangesBindingsNode->children()->arrayNode("arguments")->normalizeKeys(false)->prototype("scalar")->defaultValue([]);

		/** @var ArrayNodeDefinition $queuesNode */
		$queuesNode = $rootNode->children()->arrayNode("queues")->normalizeKeys(false)->defaultValue([])->prototype("array");
		$queuesNode->children()->booleanNode("durable")->defaultValue(false);
		$queuesNode->children()->booleanNode("exclusive")->defaultValue(false);
		$queuesNode->children()->booleanNode("auto_delete")->defaultValue(false);
		$queuesNode->children()->arrayNode("arguments")->normalizeKeys(false)->prototype("scalar")->defaultValue([]);

		/** @var ArrayNodeDefinition $queuesBindingsNode */
		$queuesBindingsNode = $queuesNode->children()->arrayNode("bindings")->normalizeKeys(false)->defaultValue([])->prototype("array");
		$queuesBindingsNode->children()->scalarNode("exchange")->isRequired();
		$queuesBindingsNode->children()->scalarNode("routing_key")->defaultValue("");
		$queuesBindingsNode->children()->arrayNode("arguments")->normalizeKeys(false)->prototype("scalar")->defaultValue([]);

		return $treeBuilder;
	}

	public function load(array $config, ContainerBuilder $container)
	{
		$container->setParameter("bunny", $this->processConfiguration($this, $config));
	}

}
