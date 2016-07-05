<?php
namespace Agere\Date;

class DateTime {

	/**
	 * @var string Дата та час у форматі Y-m-d H:i:s, яка не повинна змінюватись
	 */
	protected $_dateTimeOld;

	/**
	 * @var string Розбитий дата та час $_dateTimeOld на змінні, які можуть змінюватись
	 */
	public $Year;
	public $month;
	public $day;
	public $Hours;
	public $minutes;
	public $seconds;


	/**
	 * @param $dateTime Дата та час у форматі Y-m-d H:i:s або strtotime
	 */
	public function __construct($dateTime)
	{
		if (preg_match('/^(\d+( |-)){2}\d+( (\d+:)+\d+)?$/', $dateTime))
		{
			$strToTime = strtotime($dateTime);
			$this->_dateTimeOld = $dateTime;
		}
		else
		{
			$strToTime = $dateTime;
			$this->_dateTimeOld = date('Y-m-d H:i:s', $dateTime);
		}

		$this->Year = date('Y', $strToTime);
		$this->month = date('m', $strToTime);
		$this->day = date('d', $strToTime);
		$this->Hours = date('H', $strToTime);
		$this->minutes = date('i', $strToTime);
		$this->seconds = date('s', $strToTime);
	}

	/**
	 * Повертає дату, яка була записана в конструкторі і яка є незмінною у форматі вказаному в $formatDate
	 *
	 * @param string $formatDate
	 * @return string date
	 */
	public function getDateTimeOld($formatDate = 'Y-m-d H:i:s')
	{
		if ($formatDate == '')
			$formatDate = 'Y-m-d H:i:s';

		return date($formatDate, strtotime($this->_dateTimeOld));
	}

	/**
	 * Повертає strtotime дати та часу, які могли змінитись
	 *
	 * @return int strtotime
	 */
	public function getStrToTime()
	{
		return strtotime($this->Year.'-'.$this->month.'-'.$this->day.' '.$this->Hours.':'.$this->minutes.':'.$this->seconds);
	}

	/**
	 * Повертає дату, яка могла змінитись у форматі вказаному в $formatDate
	 *
	 * @param string $formatDate
	 * @return string date
	 */
	public function getDateTimeFormat($formatDate = 'Y-m-d H:i:s')
	{
		if ($formatDate == '')
			$formatDate = 'Y-m-d H:i:s';

		return date($formatDate, $this->getStrToTime());
	}

	/**
	 * Порівнює сьогоднішню дату із вказаною у змінній $date
	 *
	 * @param $date
	 * @return bool Повертає true, якщо сьогоднішня дата більша або рівна вказаній даті у змінній $date
	 */
	public static function compareDate($date)
	{
		return (time() >= strtotime($date)) ? true : false;
	}

	/**
	 * @param $date
	 * @param string $formatDate
	 * @param bool $isNow якщо true тоді перевірка на пусту строку,
	 * якщо строка пуста тоді повертаємо теперішню дату та час, якщо false повертаємо пусту сторку
	 *
	 * @return string
	 */
	public static function getDateFormat($date, $formatDate = 'd.m.Y H:i:s', $isNow = false)
	{
		if ($formatDate == '')
			$formatDate = 'd.m.Y H:i:s';

		if ($isNow && $date == '')
			return date($formatDate);

		return ($date != '' && $date != '0000-00-00') ? date($formatDate, strtotime($date)) : '';
	}

}