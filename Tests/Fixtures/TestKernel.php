<?php
namespace Skrz\Bundle\BunnyBundle\Tests\Fixtures;

use Skrz\Bundle\BunnyBundle\SkrzBunnyBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{

	public function __construct($config)
	{
		$this->config = $config;
		parent::__construct("test", true);
	}


	public function registerBundles()
	{
		return [
			new SkrzBunnyBundle(),
		];
	}

	public function registerContainerConfiguration(LoaderInterface $loader)
	{
		$loader->load($this->config);
	}

	public function getRootDir()
	{
		return __DIR__ . "/KernelRootDir/Kernel" . crc32($this->config);
	}

}
