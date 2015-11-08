<?php
namespace Skrz\Bundle\BunnyBundle\Tests\Fixtures;

use Skrz\Meta\AbstractMetaSpec;
use Skrz\Meta\JSON\JsonModule;
use Skrz\Meta\PHP\PhpModule;
use Skrz\Meta\Protobuf\ProtobufModule;

class TestMetaSpec extends AbstractMetaSpec
{

	protected function configure()
	{
		$this
			->match("Skrz\\Bundle\\BunnyBundle\\Tests\\Fixtures\\**")
			->notMatch("**Enum")
			->addModule(new PhpModule())
			->addModule(new JsonModule())
			->addModule(new ProtobufModule());
	}

}
