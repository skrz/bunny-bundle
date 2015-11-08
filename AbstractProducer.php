<?php
namespace Skrz\Bundle\BunnyBundle;

use Skrz\Meta\JSON\JsonMetaInterface;
use Skrz\Meta\MetaInterface;
use Skrz\Meta\Protobuf\ProtobufMetaInterface;

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

	/** @var object */
	private $meta;

	/** @var string */
	private $beforeMethod;

	/** @var string */
	private $contentType;

	/** @var BunnyManager */
	protected $manager;

	public function __construct($exchange, $routingKey, $mandatory, $immediate, $metaClassName, $beforeMethod, $contentType, BunnyManager $manager)
	{
		$this->exchange = $exchange;
		$this->routingKey = $routingKey;
		$this->mandatory = $mandatory;
		$this->immediate = $immediate;
		$this->metaClassName = $metaClassName;
		$this->beforeMethod = $beforeMethod;
		$this->contentType = $contentType;
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

		switch ($this->contentType) {
			case ContentTypes::APPLICATION_JSON:
				if ($this->meta instanceof JsonMetaInterface) {
					$message = $this->meta->toJson($message);
				} else {
					throw new BunnyException("Cannot serialize message to JSON.");
				}
				break;

			case ContentTypes::APPLICATION_PROTOBUF:
				if ($this->meta instanceof ProtobufMetaInterface) {
					$message = $this->meta->toProtobuf($message);
				} else {
					throw new BunnyException("Cannot serialize message to Protobuf.");
				}
				break;

			default:
				throw new BunnyException("Unhandled content type '{$this->contentType}'.");
		}

		if ($routingKey === null) {
			$routingKey = $this->routingKey;
		}

		$headers["content-type"] = $this->contentType;

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
