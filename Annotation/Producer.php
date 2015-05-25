<?php
namespace Skrz\Bundle\BunnyBundle\Annotation;

use Skrz\Bundle\AutowiringBundle\Annotation\Component;

/**
 * Queue producer.
 *
 * @author Jakub Kulhan <jakub.kulhan@gmail.com>
 *
 * @Annotation
 */
final class Producer extends Component
{

	/**
	 * @var string
	 */
	public $exchange = "";

	/**
	 * @var string
	 */
	public $routingKey = "";

	/**
	 * @var boolean
	 */
	public $mandatory = false;

	/**
	 * @var boolean
	 */
	public $immediate = false;

	/**
	 * @var string
	 */
	public $meta = null;

	/**
	 * @var string
	 */
	public $beforeMethod = null;

	/**
	 * @var string
	 */
	public $className;

	public static function fromArray(array $properties)
	{
		$instance = new Producer();

		foreach ($properties as $k => $v) {
			if (!property_exists($instance, $k)) {
				throw new \InvalidArgumentException("Property '{$k}' does not exists.");
			}

			$instance->$k = $v;
		}

		return $instance;
	}

}
