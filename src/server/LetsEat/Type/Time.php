<?php
namespace LetsEat\Type;


class Time
{
	/**
	 * Number of seconds since epoch
	 *
	 * @var int
	 */
	protected $timestamp;

	/**
	 * Numeric representation of the day of the week -- 0 (for Sunday) through 6 (for Saturday)
	 *
	 * @var int
	 */
	protected $dayOfWeek;

	/**
	 * 24-hour format of an hour with leading zeros	00 through 23
	 *
	 * @var int
	 */
	protected $hourOfDay;

	public function __construct($timestamp)
	{
		$this->timestamp = $timestamp;
		$this->dayOfWeek = date('w', $timestamp);
		$this->hourOfDay = date('H', $timestamp);
	}

	public function getTimestamp()
	{
		return $this->timestamp;
	}

	public function getDayOfWeek()
	{
		return $this->dayOfWeek;
	}

	public function getHourOfDay()
	{
		return $this->hourOfDay;
	}
} 