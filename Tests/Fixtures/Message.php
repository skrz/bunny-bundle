<?php
namespace Skrz\Bundle\BunnyBundle\Tests\Fixtures;

class Message
{

	/**
	 * @var int
	 */
	protected $intValue;

	/**
	 * @var float
	 */
	protected $floatValue;

	/**
	 * @var string
	 */
	protected $stringValue;

	/**
	 * @return int
	 */
	public function getIntValue()
	{
		return $this->intValue;
	}

	/**
	 * @param int $intValue
	 * @return self
	 */
	public function setIntValue($intValue)
	{
		$this->intValue = $intValue;
		return $this;
	}

	/**
	 * @return float
	 */
	public function getFloatValue()
	{
		return $this->floatValue;
	}

	/**
	 * @param float $floatValue
	 * @return self
	 */
	public function setFloatValue($floatValue)
	{
		$this->floatValue = $floatValue;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getStringValue()
	{
		return $this->stringValue;
	}

	/**
	 * @param string $stringValue
	 * @return self
	 */
	public function setStringValue($stringValue)
	{
		$this->stringValue = $stringValue;
		return $this;
	}

}
