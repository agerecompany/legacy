<?php
/**
 * Enter description here...
 *
 * @category Agere
 * @package Agere_<package>
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 17.07.14 17:37
 */
namespace Agere\Date;

class Date {

	protected  $dateTime;


	/**
	 * @param string $date
	 */
	public function __construct($date)
	{
		$this->dateTime = new \DateTime($date);
	}

	/**
	 * @param string $date
	 * @return \DateInterval
	 */
	public function diff($date)
	{
		return $this->dateTime->diff(new \DateTime($date));
	}

}