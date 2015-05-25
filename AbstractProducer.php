<?php
namespace Skrz\Bundle\BunnyBundle;

use Bunny\Channel;
use Skrz\Meta\JSON\JsonMetaInterface;
use Skrz\Meta\MetaInterface;

class AbstractProducer
{

	/** @var string */
	private $exchange;

	/** @var string */
	private $routingKey;

	/** @var boolean */
	private $mandatory;

	/** @var boolean */
	private $immediate;

	/** @var string */
	private $metaClassName;

	/** @var JsonMetaInterface */
	private $meta;

	/** @var string */
	private $beforeMethod;

	/** @var BunnyManager */
	protected $manager;

	public function __construct($exchange, $routingKey, $mandatory, $immediate, $metaClassName, $beforeMethod, BunnyManager $manager)
	{
		$this->exchange = $exchange;
		$this->routingKey = $routingKey;
		$this->mandatory = $mandatory;
		$this->immediate = $immediate;
		$this->metaClassName = $metaClassName;
		$this->beforeMethod = $beforeMethod;
		$this->manager = $manager;
	}

	public function createMeta()
	{
		if ($this->metaClassName) {
			/** @var MetaInterface $metaClassName */
			$metaClassName = $this->metaClassName;
			return $metaClassName::getInstance();
		} else {
			return null;
		}
	}

	public function getMeta()
	{
		if ($this->meta === null) {
			$this->meta = $this->createMeta();
		}
		return $this->meta;
	}

	public function publish($message, $routingKey = null, array $headers = [])
	{
		if (!$this->getMeta()) {
			throw new BunnyException("Could not create meta class {$this->metaClassName}.");
		}

		if (is_string($message)) {
			$message = $this->meta->fromJson($message);
		}

		if ($this->beforeMethod) {
			$this->{$this->beforeMethod}($message, $this->manager->getChannel());
		}

		$message = $this->meta->toJson($message);

		if ($routingKey === null) {
			$routingKey = $this->routingKey;
		}

		$headers["content-type"] = "application/json";

		$this->manager->getChannel()->publish(
			$message,
			$headers,
			$this->exchange,
			$routingKey,
			$this->mandatory,
			$this->immediate
		);
	}

}
