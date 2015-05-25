<?php
namespace Skrz\Bundle\BunnyBundle\Annotation;

use Skrz\Bundle\AutowiringBundle\Annotation\Component;

/**
 * Queue consumer.
 *
 * @author Jakub Kulhan <jakub.kulhan@gmail.com>
 *
 * @Annotation
 */
final class Consumer extends Component
{

	/**
	 * @var string
	 */
	public $exchange = null;

	/**
	 * @var string
	 */
	public $routingKey = "";

	/**
	 * @var string
	 */
	public $queue = null;

	/**
	 * @var string
	 */
	public $consumerTag = "";

	/**
	 * @var boolean
	 */
	public $noLocal = false;

	/**
	 * @var boolean
	 */
	public $noAck = false;

	/**
	 * @var boolean
	 */
	public $exclusive = false;

	/**
	 * @var boolean
	 */
	public $nowait = false;

	/**
	 * @var array
	 */
	public $arguments = [];

	/**
	 * @var int
	 */
	public $prefetchCount = null;

	/**
	 * @var int
	 */
	public $prefetchSize = null;

	/**
	 * @var string
	 */
	public $meta = null;

	/**
	 * @var string
	 */
	public $method = "handleMessage";

	/**
	 * @var string
	 */
	public $setUpMethod = null;

	/**
	 * @var string
	 */
	public $tickMethod = null;

	/**
	 * @var float
	 */
	public $tickSeconds = null;

	/**
	 * @var int
	 */
	public $maxMessages = null;

	/**
	 * @var float
	 */
	public $maxSeconds = null;

	/**
	 * @var string
	 */
	public $className;

	public static function fromArray(array $properties)
	{
		$instance = new Consumer();

		foreach ($properties as $k => $v) {
			if (!property_exists($instance, $k)) {
				throw new \InvalidArgumentException("Property '{$k}' does not exists.");
			}

			$instance->$k = $v;
		}

		return $instance;
	}

}
