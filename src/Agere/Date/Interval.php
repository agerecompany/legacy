<?php
/**
 * Improve wrapper for DateInterval
 *
 * @category Agere
 * @package Agere_Date
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 12.07.13 17:56
 */

namespace Agere\Date;


class Interval {

	protected $dateInterval;

	public function __construct(\DateInterval $dateInterval) {
		$this->dateInterval = $dateInterval;
	}

	/**
	 * Get interval in seconds
	 *
	 * 86400 is the number of seconds in a day
	 * 3600 is the number of seconds in an hour
	 * 60 is the number of seconds in a minute
	 *
	 * @return int
	 * @link http://stackoverflow.com/a/14277647
	 */
	public function toSeconds() {
		$interval = $this->dateInterval;
		$seconds = $interval->days * 86400 + $interval->h * 3600 + $interval->i * 60 + $interval->s;

		return $seconds;
	}

	/**
	 * Get interval in minutes
	 *
	 * @return int
	 * @link http://stackoverflow.com/a/5088533
	 */
	public function toMinute() {
		$minutes = floor($this->toSeconds() / 60);

		return $minutes;
	}

	/**
	 * Get interval in hours
	 *
	 * @return int
	 */
	public function toHours() {
		$interval = $this->dateInterval;
		$hours = $interval->h + ($interval->d * 24);

		return $hours;
	}

	/**
	 * Get interval in days
	 *
	 * @return int
	 */
	public function toDays() {
		return $this->dateInterval->days;
	}

	/**
	 * Get interval in weeks
	 *
	 * @return int
	 */
	public function toWeeks() {
		$weeks = floor($this->toDays() / 7);
		return $weeks;
	}

	/**
	 * Get interval in months
	 *
	 * @return int
	 */
	public function toMonths() {
		$months = floor($this->toDays() / 30);

		return $months;
	}

	/**
	 * Get interval in years
	 *
	 * @return int
	 * @link http://icodesnippet.com/snippet/php/php-convert-seconds-to-time-years-months-days-hours
	 */
	public function toYears() {
		$years = floor($this->toSeconds() / 31556926);

		return $years;
	}

}