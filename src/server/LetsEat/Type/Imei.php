<?php
namespace LetsEat\Type;

class Imei
{
	private $value;

	public function __construct($value)
	{
		$this->value = $value;
	}

	public function validate()
	{
		return true;
	}

	public function getValue()
	{
		return $this->value;
	}
} 