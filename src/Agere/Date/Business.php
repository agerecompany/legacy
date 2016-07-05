<?php
/**
 * Class for calculate business time logic
 *
 * @category Agere
 * @package Agere_Date
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 08.07.13 20:40
 */

namespace Agere\Date;

use Agere\ArrayUtil\ArrayUtil;

class Business {

	/**
	 * Business time of week
	 *
	 * @var array
	 */
	protected $businessTimes = array(
		array('monday 09:00', 'monday 18:00'),
		array('tuesday 09:00', 'tuesday 18:00'),
		array('wednesday 09:00', 'wednesday 18:00'),
		array('thursday 09:00', 'thursday 18:00'),
		array('friday 09:00', 'friday 18:00'),
		//array('saturday 10:00', 'sunday 18:00'),
		//array('sunday 10:00', 'monday 01:00'),
	);

	/**
	 * Convert any time to timestamp
	 *
	 * @param $sometime
	 * @return int $time Return timestamp of given data
	 */
	public function convertToTimestamp($sometime) {
		$time = $sometime;
		if (!is_int($sometime)) {
			$time = strtotime($sometime);
		}
		return $time;
	}

	/**
	 * Normalize time to count from 0
	 *
	 * Normalize the time to count seconds from monday 00:00, regardless it the time is in the future or past.
	 *
	 * @link http://stackoverflow.com/a/14115999
	 */
	public function timeOfWeek($time) {
		$time = $this->convertToTimestamp($time);
		$secondsInWeek = (7 * 24 * 3600);
		return (($time - strtotime('monday 00:00')) % $secondsInWeek + $secondsInWeek) % $secondsInWeek;
	}

	/**
	 * In time parameter in rage of business time
	 *
	 * @param $time
	 * @return bool
	 */
	public function isBusiness($time) {
		return (bool) $this->getBusinessTime($time);
	}

	/**
	 * Get business period for time
	 *
	 * @param $time
	 * @return bool|array Return array which consist business period otherwise if time isn't in rage one if business period return false
	 */
	public function getBusinessTime($time) {
		$time = $this->timeOfWeek($time);

		$opened = $this->prepareBusinessTimes();
		foreach ($opened as $key => $openday) {
			list($open, $close) = $openday;
			if ($open < $close) {
				if ($time >= $open && $time <= $close) return $this->businessTimes[$key];
			} else {
				if ($time >= $open || $time <= $close) return $this->businessTimes[$key]; // Special case sunday -> monday
			}
		}

		return false;
	}

	/**
	 * Similar to getBusinessTime method except if business period not found for time then return next business period
	 */
	public function getBusinessPeriod($time) {
		$time = $this->convertToTimestamp($time);
		if ($businessPeriod = $this->getBusinessTime($time)) {
			return $businessPeriod;
		}

		$onlyBegin = array();
		$onlyClose = array();
		$opened = $this->prepareBusinessTimes();
		foreach ($opened as $key => $openday) {
			list($open, $close) = $openday;
			$onlyBegin[] = $open;
			$onlyClose[] = $close;
		}

		$timeOfWeek = $this->timeOfWeek($time);
		$keyOfPeriod = 0;

		// if checking date is not more than last business day of week then find appropriate business day of week
		if ($timeOfWeek < end($onlyClose)) {
			$keyOfPeriod = ArrayUtil::create($onlyClose)->findMinMaxKey($timeOfWeek, 'max');
		}

		return $this->businessTimes[$keyOfPeriod];
	}

	public function prepareBusinessTimes() {
		static $businessTime = array();
		if (!$businessTime) {
			foreach ($this->businessTimes as $openday) {
				$open = $this->timeOfWeek($openday[0]);
				$close = $this->timeOfWeek($openday[1]);
				$businessTime[] = array($open, $close);
			}
		}
		return $businessTime;
	}

	/**
	 * The function returns the no. of business days between two dates and it skips the holidays
	 *
	 * Example:
	 * $holidays = array("2008-12-25", "2008-12-26", "2009-01-01");
	 * echo getWorkingDays("2008-12-22", "2009-01-02", $holidays);
	 * => will return 7
	 *
	 * @link http://stackoverflow.com/a/336175
	 * @todo Create class variable $holidays
	 */
	public function getWorkingDays($startDate, $endDate, $holidays){
		// do strtotime calculations just once
		$endDate = strtotime($endDate);
		$startDate = strtotime($startDate);
		//The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
		//We add one to inlude both dates in the interval.
		$days = ($endDate - $startDate) / 86400 + 1;
		$noFullWeeks = floor($days / 7);
		$noRemainingDays = fmod($days, 7);
		//It will return 1 if it's Monday,.. ,7 for Sunday
		$the_first_day_of_week = date("N", $startDate);
		$theLastDayOfWeek = date("N", $endDate);
		//---->The two can be equal in leap years when february has 29 days, the equal sign is added here
		//In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
		if ($the_first_day_of_week <= $theLastDayOfWeek) {
			if ($the_first_day_of_week <= 6 && 6 <= $theLastDayOfWeek) {
				$noRemainingDays--;
			}
			if ($the_first_day_of_week <= 7 && 7 <= $theLastDayOfWeek) {
				$noRemainingDays--;
			}
		} else {
			// (edit by Tokes to fix an edge case where the start day was a Sunday
			// and the end day was NOT a Saturday)
			// the day of the week for start is later than the day of the week for end
			if ($the_first_day_of_week == 7) {
				// if the start date is a Sunday, then we definitely subtract 1 day
				$noRemainingDays--;
				if ($theLastDayOfWeek == 6) {
					// if the end date is a Saturday, then we subtract another day
					$noRemainingDays--;
				}
			} else {
				// the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
				// so we skip an entire weekend and subtract 2 days
				$noRemainingDays -= 2;
			}
		}
		//The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
		//---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
		$workingDays = $noFullWeeks * 5;
		if ($noRemainingDays > 0) {
			$workingDays += $noRemainingDays;
		}
		//We subtract the holidays
		foreach ($holidays as $holiday) {
			$timestamp = strtotime($holiday);
			//If the holiday doesn't fall in weekend
			if ($startDate <= $timestamp && $timestamp <= $endDate && date("N", $timestamp) != 6 && date("N", $timestamp) != 7)
				$workingDays--;
		}

		return floor($workingDays);
	}

	/**
	 * Check if a date is in a given range
	 *
	 * Example:
	 * $start_date = '2009-06-17';
	 * $end_date = '2009-09-05';
	 * $date_from_user = '2009-08-28';
	 * check_in_range($start_date, $end_date, $date_from_user);
	 *
	 * @param $startDate
	 * @param $endDate
	 * @param $dateFromUser
	 * @return bool
	 * @link http://stackoverflow.com/a/976712
	 */
	function checkInRange($startDate, $endDate, $dateFromUser) {
		$startTs = $this->convertToTimestamp($startDate);
		$endTs = $this->convertToTimestamp($endDate);
		$userTs = $this->convertToTimestamp($dateFromUser);

		// Check that user date is between start & end
		return (($userTs >= $startTs) && ($userTs <= $endTs));
	}

	/**
	 * Plus business hours to date
	 *
	 * Attention! Work correct only for current date.
	 *
	 * @param string $hoursConsideration
	 * @param \DateTime $datetime Time against which will begin deducting hours
	 * @return \DateTime
	 * @todo Improve for passed date
	 */
	public function plusBusinessHours($hoursConsideration, \DateTime $datetime = null) {
		if (!$datetime) {
			$datetime = new \DateTime();
		}

		list($beginTime, $endTime) = $this->getBusinessPeriod($datetime->getTimestamp());

		$begin = new \DateTime($beginTime);
		$end = new \DateTime($endTime);

		if ($this->checkInRange($begin->getTimestamp(), $end->getTimestamp(), $datetime->getTimestamp())) {
			$diff = $datetime->diff($end);
		} else {
			$diff = $begin->diff($end);
		}
		$hoursLeft = $diff->format('%h'); // @link http://php.net/manual/en/dateinterval.format.php

		if ($hoursConsideration > $hoursLeft) {
			$hoursRemain = $hoursConsideration - $hoursLeft;
			return $this->plusBusinessHours($hoursRemain, $begin->modify('+1 day'));
		} else {
			return $begin->modify(sprintf('+ %d hours', $hoursConsideration));
		}
	}

}