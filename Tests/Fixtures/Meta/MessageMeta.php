<?php
namespace Skrz\Bundle\BunnyBundle\Tests\Fixtures\Meta;

use Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message;
use Skrz\Meta\JSON\JsonMetaInterface;
use Skrz\Meta\MetaInterface;
use Skrz\Meta\PHP\PhpMetaInterface;
use Skrz\Meta\Protobuf\Binary;
use Skrz\Meta\Protobuf\ProtobufException;
use Skrz\Meta\Protobuf\ProtobufMetaInterface;
use Skrz\Meta\Stack;

/**
 * Meta class for \Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message
 *
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * !!!                                                     !!!
 * !!!   THIS CLASS HAS BEEN AUTO-GENERATED, DO NOT EDIT   !!!
 * !!!                                                     !!!
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 */
class MessageMeta extends Message implements MetaInterface, PhpMetaInterface, JsonMetaInterface, ProtobufMetaInterface
{
	const INT_VALUE_PROTOBUF_FIELD = 1;
	const FLOAT_VALUE_PROTOBUF_FIELD = 2;
	const STRING_VALUE_PROTOBUF_FIELD = 3;

	/** @var MessageMeta */
	private static $instance;

	/**
	 * Mapping from group name to group ID for fromArray() and toArray()
	 *
	 * @var string[]
	 */
	private static $groups = array('' => 1, 'json:' => 2);


	/**
	 * Constructor
	 */
	private function __construct()
	{
		self::$instance = $this; // avoids cyclic dependency stack overflow
	}


	/**
	 * Returns instance of this meta class
	 *
	 * @return MessageMeta
	 */
	public static function getInstance()
	{
		if (self::$instance === null) {
			new self(); // self::$instance assigned in __construct
		}
		return self::$instance;
	}


	/**
	 * Creates new instance of \Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @return Message
	 */
	public static function create()
	{
		switch (func_num_args()) {
			case 0:
				return new Message();
			case 1:
				return new Message(func_get_arg(0));
			case 2:
				return new Message(func_get_arg(0), func_get_arg(1));
			case 3:
				return new Message(func_get_arg(0), func_get_arg(1), func_get_arg(2));
			case 4:
				return new Message(func_get_arg(0), func_get_arg(1), func_get_arg(2), func_get_arg(3));
			case 5:
				return new Message(func_get_arg(0), func_get_arg(1), func_get_arg(2), func_get_arg(3), func_get_arg(4));
			case 6:
				return new Message(func_get_arg(0), func_get_arg(1), func_get_arg(2), func_get_arg(3), func_get_arg(4), func_get_arg(5));
			case 7:
				return new Message(func_get_arg(0), func_get_arg(1), func_get_arg(2), func_get_arg(3), func_get_arg(4), func_get_arg(5), func_get_arg(6));
			case 8:
				return new Message(func_get_arg(0), func_get_arg(1), func_get_arg(2), func_get_arg(3), func_get_arg(4), func_get_arg(5), func_get_arg(6), func_get_arg(7));
			default:
				throw new \InvalidArgumentException('More than 8 arguments supplied, please be reasonable.');
		}
	}


	/**
	 * Resets properties of \Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message to default values
	 *
	 *
	 * @param Message $object
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @return void
	 */
	public static function reset($object)
	{
		if (!($object instanceof Message)) {
			throw new \InvalidArgumentException('You have to pass object of class Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message.');
		}
		$object->intValue = NULL;
		$object->floatValue = NULL;
		$object->stringValue = NULL;
	}


	/**
	 * Computes hash of \Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message
	 *
	 * @param object $object
	 * @param string|resource $algoOrCtx
	 * @param bool $raw
	 *
	 * @return string|void
	 */
	public static function hash($object, $algoOrCtx = 'md5', $raw = FALSE)
	{
		if (is_string($algoOrCtx)) {
			$ctx = hash_init($algoOrCtx);
		} else {
			$ctx = $algoOrCtx;
		}

		if (isset($object->intValue)) {
			hash_update($ctx, 'intValue');
			hash_update($ctx, (string)$object->intValue);
		}

		if (isset($object->floatValue)) {
			hash_update($ctx, 'floatValue');
			hash_update($ctx, (string)$object->floatValue);
		}

		if (isset($object->stringValue)) {
			hash_update($ctx, 'stringValue');
			hash_update($ctx, (string)$object->stringValue);
		}

		if (is_string($algoOrCtx)) {
			return hash_final($ctx, $raw);
		} else {
			return null;
		}
	}


	/**
	 * Creates \Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message object from array
	 *
	 * @param array $input
	 * @param string $group
	 * @param Message $object
	 *
	 * @throws \Exception
	 *
	 * @return Message
	 */
	public static function fromArray($input, $group = NULL, $object = NULL)
	{
		if (!isset(self::$groups[$group])) {
			throw new \InvalidArgumentException('Group \'' . $group . '\' not supported for ' . 'Skrz\\Bundle\\BunnyBundle\\Tests\\Fixtures\\Message' . '.');
		} else {
			$id = self::$groups[$group];
		}

		if ($object === null) {
			$object = new Message();
		} elseif (!($object instanceof Message)) {
			throw new \InvalidArgumentException('You have to pass object of class Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message.');
		}

		if (($id & 1) > 0 && isset($input['intValue'])) {
			$object->intValue = $input['intValue'];
		} elseif (($id & 1) > 0 && array_key_exists('intValue', $input) && $input['intValue'] === null) {
			$object->intValue = null;
		}
		if (($id & 2) > 0 && isset($input['intValue'])) {
			$object->intValue = $input['intValue'];
		} elseif (($id & 2) > 0 && array_key_exists('intValue', $input) && $input['intValue'] === null) {
			$object->intValue = null;
		}

		if (($id & 1) > 0 && isset($input['floatValue'])) {
			$object->floatValue = $input['floatValue'];
		} elseif (($id & 1) > 0 && array_key_exists('floatValue', $input) && $input['floatValue'] === null) {
			$object->floatValue = null;
		}
		if (($id & 2) > 0 && isset($input['floatValue'])) {
			$object->floatValue = $input['floatValue'];
		} elseif (($id & 2) > 0 && array_key_exists('floatValue', $input) && $input['floatValue'] === null) {
			$object->floatValue = null;
		}

		if (($id & 1) > 0 && isset($input['stringValue'])) {
			$object->stringValue = $input['stringValue'];
		} elseif (($id & 1) > 0 && array_key_exists('stringValue', $input) && $input['stringValue'] === null) {
			$object->stringValue = null;
		}
		if (($id & 2) > 0 && isset($input['stringValue'])) {
			$object->stringValue = $input['stringValue'];
		} elseif (($id & 2) > 0 && array_key_exists('stringValue', $input) && $input['stringValue'] === null) {
			$object->stringValue = null;
		}

		return $object;
	}


	/**
	 * Serializes \Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message to array
	 *
	 * @param Message $object
	 * @param string $group
	 * @param array $filter
	 *
	 * @throws \Exception
	 *
	 * @return array
	 */
	public static function toArray($object, $group = NULL, array $filter = NULL)
	{
		if ($object === null) {
			return null;
		}
		if (!isset(self::$groups[$group])) {
			throw new \InvalidArgumentException('Group \'' . $group . '\' not supported for ' . 'Skrz\\Bundle\\BunnyBundle\\Tests\\Fixtures\\Message' . '.');
		} else {
			$id = self::$groups[$group];
		}

		if (!($object instanceof Message)) {
			throw new \InvalidArgumentException('You have to pass object of class Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message.');
		}

		if (Stack::$objects === null) {
			Stack::$objects = new \SplObjectStorage();
		}

		if (Stack::$objects->contains($object)) {
			return null;
		}

		Stack::$objects->attach($object);

		try {
			$output = array();

			if (($id & 1) > 0 && ($filter === null || isset($filter['intValue']))) {
				$output['intValue'] = $object->intValue;
			}
			if (($id & 2) > 0 && ((isset($object->intValue) && $filter === null) || isset($filter['intValue']))) {
				$output['intValue'] = $object->intValue;
			}

			if (($id & 1) > 0 && ($filter === null || isset($filter['floatValue']))) {
				$output['floatValue'] = $object->floatValue;
			}
			if (($id & 2) > 0 && ((isset($object->floatValue) && $filter === null) || isset($filter['floatValue']))) {
				$output['floatValue'] = $object->floatValue;
			}

			if (($id & 1) > 0 && ($filter === null || isset($filter['stringValue']))) {
				$output['stringValue'] = $object->stringValue;
			}
			if (($id & 2) > 0 && ((isset($object->stringValue) && $filter === null) || isset($filter['stringValue']))) {
				$output['stringValue'] = $object->stringValue;
			}

		} catch (\Exception $e) {
			Stack::$objects->detach($object);
			throw $e;
		}

		Stack::$objects->detach($object);
		return $output;
	}


	/**
	 * Creates \Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message object from object
	 *
	 * @param object $input
	 * @param string $group
	 * @param Message $object
	 *
	 * @throws \Exception
	 *
	 * @return Message
	 */
	public static function fromObject($input, $group = NULL, $object = NULL)
	{
		$input = (array)$input;

		if (!isset(self::$groups[$group])) {
			throw new \InvalidArgumentException('Group \'' . $group . '\' not supported for ' . 'Skrz\\Bundle\\BunnyBundle\\Tests\\Fixtures\\Message' . '.');
		} else {
			$id = self::$groups[$group];
		}

		if ($object === null) {
			$object = new Message();
		} elseif (!($object instanceof Message)) {
			throw new \InvalidArgumentException('You have to pass object of class Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message.');
		}

		if (($id & 1) > 0 && isset($input['intValue'])) {
			$object->intValue = $input['intValue'];
		} elseif (($id & 1) > 0 && array_key_exists('intValue', $input) && $input['intValue'] === null) {
			$object->intValue = null;
		}
		if (($id & 2) > 0 && isset($input['intValue'])) {
			$object->intValue = $input['intValue'];
		} elseif (($id & 2) > 0 && array_key_exists('intValue', $input) && $input['intValue'] === null) {
			$object->intValue = null;
		}

		if (($id & 1) > 0 && isset($input['floatValue'])) {
			$object->floatValue = $input['floatValue'];
		} elseif (($id & 1) > 0 && array_key_exists('floatValue', $input) && $input['floatValue'] === null) {
			$object->floatValue = null;
		}
		if (($id & 2) > 0 && isset($input['floatValue'])) {
			$object->floatValue = $input['floatValue'];
		} elseif (($id & 2) > 0 && array_key_exists('floatValue', $input) && $input['floatValue'] === null) {
			$object->floatValue = null;
		}

		if (($id & 1) > 0 && isset($input['stringValue'])) {
			$object->stringValue = $input['stringValue'];
		} elseif (($id & 1) > 0 && array_key_exists('stringValue', $input) && $input['stringValue'] === null) {
			$object->stringValue = null;
		}
		if (($id & 2) > 0 && isset($input['stringValue'])) {
			$object->stringValue = $input['stringValue'];
		} elseif (($id & 2) > 0 && array_key_exists('stringValue', $input) && $input['stringValue'] === null) {
			$object->stringValue = null;
		}

		return $object;
	}


	/**
	 * Serializes \Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message to object
	 *
	 * @param Message $object
	 * @param string $group
	 * @param array $filter
	 *
	 * @throws \Exception
	 *
	 * @return object
	 */
	public static function toObject($object, $group = NULL, array $filter = NULL)
	{
		if ($object === null) {
			return null;
		}
		if (!isset(self::$groups[$group])) {
			throw new \InvalidArgumentException('Group \'' . $group . '\' not supported for ' . 'Skrz\\Bundle\\BunnyBundle\\Tests\\Fixtures\\Message' . '.');
		} else {
			$id = self::$groups[$group];
		}

		if (!($object instanceof Message)) {
			throw new \InvalidArgumentException('You have to pass object of class Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message.');
		}

		if (Stack::$objects === null) {
			Stack::$objects = new \SplObjectStorage();
		}

		if (Stack::$objects->contains($object)) {
			return null;
		}

		Stack::$objects->attach($object);

		try {
			$output = array();

			if (($id & 1) > 0 && ($filter === null || isset($filter['intValue']))) {
				$output['intValue'] = $object->intValue;
			}
			if (($id & 2) > 0 && ((isset($object->intValue) && $filter === null) || isset($filter['intValue']))) {
				$output['intValue'] = $object->intValue;
			}

			if (($id & 1) > 0 && ($filter === null || isset($filter['floatValue']))) {
				$output['floatValue'] = $object->floatValue;
			}
			if (($id & 2) > 0 && ((isset($object->floatValue) && $filter === null) || isset($filter['floatValue']))) {
				$output['floatValue'] = $object->floatValue;
			}

			if (($id & 1) > 0 && ($filter === null || isset($filter['stringValue']))) {
				$output['stringValue'] = $object->stringValue;
			}
			if (($id & 2) > 0 && ((isset($object->stringValue) && $filter === null) || isset($filter['stringValue']))) {
				$output['stringValue'] = $object->stringValue;
			}

		} catch (\Exception $e) {
			Stack::$objects->detach($object);
			throw $e;
		}

		Stack::$objects->detach($object);
		return (object)$output;
	}


	/**
	 * Creates \Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message from JSON array / JSON serialized string
	 *
	 * @param array|string $json
	 * @param string $group
	 * @param Message $object
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @return Message
	 */
	public static function fromJson($json, $group = NULL, $object = NULL)
	{
		if (is_array($json)) {
			// ok, nothing to do here
		} elseif (is_string($json)) {
			$decoded = json_decode($json, true);
			if ($decoded === null && $json !== '' && strcasecmp($json, 'null')) {
				throw new \InvalidArgumentException('Could not decode given JSON: ' . $json . '.');
			}
			$json = $decoded;
		} else {
			throw new \InvalidArgumentException('Expected array, or string, ' . gettype($json) . ' given.');
		}

		return self::fromObject($json, 'json:' . $group, $object);
	}


	/**
	 * Serializes \Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message to JSON string
	 *
	 * @param Message $object
	 * @param string $group
	 * @param array|int $filterOrOptions
	 * @param int $options
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @return string
	 */
	public static function toJson($object, $group = NULL, $filterOrOptions = NULL, $options = 0)
	{
		if (is_int($filterOrOptions)) {
			$options = $filterOrOptions;
			$filterOrOptions = null;
		}

		return json_encode(self::toObject($object, 'json:' . $group, $filterOrOptions), $options);
	}


	/**
	 * Serializes \Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message to JSON string (only for BC, TO BE REMOVED)
	 *
	 * @param Message $object
	 * @param string $group
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @deprecated
	 *
	 * @return string
	 */
	public static function toJsonString($object, $group = NULL)
	{
		return self::toJson($object, $group);
	}


	/**
	 * Serializes \Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message to JSON pretty string (only for BC, TO BE REMOVED)
	 *
	 * @param Message $object
	 * @param string $group
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @deprecated
	 *
	 * @return string
	 */
	public static function toJsonStringPretty($object, $group = NULL)
	{
		return self::toJson($object, $group, JSON_PRETTY_PRINT);
	}


	/**
	 * Creates \Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message from array of JSON-serialized properties
	 *
	 * @param array $input
	 * @param string $group
	 * @param Message $object
	 *
	 * @return Message
	 */
	public static function fromArrayOfJson($input, $group = NULL, $object = NULL)
	{
		$group = 'json:' . $group;
		if (!isset(self::$groups[$group])) {
			throw new \InvalidArgumentException('Group \'' . $group . '\' not supported for ' . 'Skrz\\Bundle\\BunnyBundle\\Tests\\Fixtures\\Message' . '.');
		} else {
			$id = self::$groups[$group];
		}

		/** @var object $input */
		return self::fromObject($input, $group, $object);
	}


	/**
	 * Transforms \Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message into array of JSON-serialized strings
	 *
	 * @param Message $object
	 * @param string $group
	 * @param array|int $filterOrOptions
	 * @param int $options
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @return array
	 */
	public static function toArrayOfJson($object, $group = NULL, $filterOrOptions = 0, $options = 0)
	{
		if (is_int($filterOrOptions)) {
			$options = $filterOrOptions;
			$filter = null;
		} else {
			$filter = $filterOrOptions;
		}

		$group = 'json:' . $group;
		if (!isset(self::$groups[$group])) {
			throw new \InvalidArgumentException('Group \'' . $group . '\' not supported for ' . 'Skrz\\Bundle\\BunnyBundle\\Tests\\Fixtures\\Message' . '.');
		} else {
			$id = self::$groups[$group];
		}

		$output = (array)self::toObject($object, $group, $filter);

		return $output;
	}


	/**
	 * Creates \Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message object from serialized Protocol Buffers message.
	 *
	 * @param string $input
	 * @param Message $object
	 * @param int $start
	 * @param int $end
	 *
	 * @throws \Exception
	 *
	 * @return Message
	 */
	public static function fromProtobuf($input, $object = NULL, &$start = 0, $end = NULL)
	{
		if ($object === null) {
			$object = new Message();
		}

		if ($end === null) {
			$end = strlen($input);
		}

		while ($start < $end) {
			$tag = Binary::decodeVarint($input, $start);
			$wireType = $tag & 0x7;
			$number = $tag >> 3;
			switch ($number) {
				case 1:
					if ($wireType !== 0) {
						throw new ProtobufException('Unexpected wire type ' . $wireType . ', expected 0.', $number);
					}
					$object->intValue = Binary::decodeVarint($input, $start);
					break;
				case 2:
					if ($wireType !== 1) {
						throw new ProtobufException('Unexpected wire type ' . $wireType . ', expected 1.', $number);
					}
					$expectedStart = $start + 8;
					if ($expectedStart > $end) {
						throw new ProtobufException('Not enough data.');
					}
					$object->floatValue = Binary::decodeDouble($input, $start);
					if ($start !== $expectedStart) {
						throw new ProtobufException('Unexpected start. Expected ' . $expectedStart . ', got ' . $start . '.', $number);
					}
					break;
				case 3:
					if ($wireType !== 2) {
						throw new ProtobufException('Unexpected wire type ' . $wireType . ', expected 2.', $number);
					}
					$length = Binary::decodeVarint($input, $start);
					$expectedStart = $start + $length;
					if ($expectedStart > $end) {
						throw new ProtobufException('Not enough data.');
					}
					$object->stringValue = substr($input, $start, $length);
					$start += $length;
					if ($start !== $expectedStart) {
						throw new ProtobufException('Unexpected start. Expected ' . $expectedStart . ', got ' . $start . '.', $number);
					}
					break;
				default:
					switch ($wireType) {
						case 0:
							Binary::decodeVarint($input, $start);
							break;
						case 1:
							$start += 8;
							break;
						case 2:
							$start += Binary::decodeVarint($input, $start);
							break;
						case 5:
							$start += 4;
							break;
						default:
							throw new ProtobufException('Unexpected wire type ' . $wireType . '.', $number);
					}
			}
		}

		return $object;
	}


	/**
	 * Serialized \Skrz\Bundle\BunnyBundle\Tests\Fixtures\Message to Protocol Buffers message.
	 *
	 * @param Message $object
	 * @param array $filter
	 *
	 * @throws \Exception
	 *
	 * @return string
	 */
	public static function toProtobuf($object, array $filter = NULL)
	{
		$output = '';

		if (isset($object->intValue) && ($filter === null || isset($filter['intValue']))) {
			$output .= "\x08";
			$output .= Binary::encodeVarint($object->intValue);
		}

		if (isset($object->floatValue) && ($filter === null || isset($filter['floatValue']))) {
			$output .= "\x11";
			$output .= Binary::encodeDouble($object->floatValue);
		}

		if (isset($object->stringValue) && ($filter === null || isset($filter['stringValue']))) {
			$output .= "\x1a";
			$output .= Binary::encodeVarint(strlen($object->stringValue));
			$output .= $object->stringValue;
		}

		return $output;
	}

}
