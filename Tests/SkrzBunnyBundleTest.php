<?php
namespace Skrz\Bundle\BunnyBundle\Tests;

use Skrz\Bundle\BunnyBundle\BunnyManager;
use Skrz\Bundle\BunnyBundle\Command\SetupCommand;
use Skrz\Bundle\BunnyBundle\DependencyInjection\Compiler\BunnyCompilerPass;
use Skrz\Bundle\BunnyBundle\SkrzBunnyBundle;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SkrzBunnyBundleTest extends \PHPUnit_Framework_TestCase
{

	/** @var SkrzBunnyBundle */
	private $skrzBunnyBundle;

	protected function setUp()
	{
		$this->skrzBunnyBundle = new SkrzBunnyBundle();
	}

	public function testGetContainerExtension()
	{
		$this->assertInstanceOf(
			"Skrz\\Bundle\\BunnyBundle\\DependencyInjection\\SkrzBunnyExtension",
			$this->skrzBunnyBundle->getContainerExtension()
		);
	}

	public function testBuild()
	{
		$containerBuilder = new ContainerBuilder();
		$this->skrzBunnyBundle->build($containerBuilder);
		$passConfig = $containerBuilder->getCompiler()->getPassConfig();
		$optimizationPasses = $passConfig->getOptimizationPasses();

		$contains = false;
		foreach ($optimizationPasses as $pass) {
			if ($pass instanceof BunnyCompilerPass) {
				$contains = true;
			}
		}

		if (!$contains) {
			$this->fail("Bunny hasn't registered compiler pass.");
		}
	}

	public function testRegisterCommands()
	{
		$application = $this->getMock("Symfony\\Component\\Console\\Application");
		$container = $this->getMock("Symfony\\Component\\DependencyInjection\\ContainerInterface");
		$container->method("get")->willReturnCallback(function ($id) {
			if (in_array($id, ["command.bunny.setup", "command.bunny.consumer", "command.bunny.producer"])) {
				return new Command($id);
			}

			throw new \InvalidArgumentException("Service '{$id}' does not exist.");
		});

		/** @var Application $application */
		/** @var ContainerInterface $container */
		$this->skrzBunnyBundle->setContainer($container);
		$this->skrzBunnyBundle->registerCommands($application);
	}

}
