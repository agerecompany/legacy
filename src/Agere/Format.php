<?php
namespace Agere;

class Format {

	private static $settings = array(
		'currency' => array(
			'symbol' => "грн.",		// default currency symbol is '$'
			'format' => "%v %s", 	// controls output: %s = symbol, %v = value/number (can be object: see below)
			'decimal' => " ",  		// decimal point separator
			'thousand' => " ",  	// thousands separator
			'precision' => 0   		// decimal places
		),
		'number' => array(
			'precision' => 0,  		// default precision on numbers is 0
			'thousand' => " ",
			'decimal' => " "
		),
		'date' => array(
			'format' => 'd.m.Y'
		),
		'time' => array(
			'format' => 'H:i:s'
		)
	);

	public function settings($settings) {
		self::$settings = $settings;
	}

	/**
	 * Example
	 * 1234567,123 -> 1 234 567
	 * @param int|float $num
	 * @param bool $isEmpty, check is empty string
	 *
	 * @return string|int
	 */
	public static function number($num, $isEmpty = false, $decimaal = null, $thousand = null)
	{
		$setting = self::$settings['number'];
		$decimaal = (is_null($decimaal)) ? $setting['decimal'] : $decimaal;
		$thousand = (is_null($thousand)) ? $setting['thousand'] : $thousand;

		$numFormat = number_format($num, $setting['precision'], $decimaal, $thousand);

		return ($isEmpty && empty($numFormat)) ? '' : $numFormat;
	}

	/**
	 * Example
	 * 1234567,123 -> 1 234 567 грн.
	 * @param int|fload $num
	 */
	public static function money($num) {
		$setting = self::$settings['currency'];

		$number = number_format($num, $setting['precision'], $setting['decimal'], $setting['thousand']);

		$value = str_replace('%v', $number, $setting['format']);
		$string = str_replace('%s', $setting['symbol'], $value);
		return $string;
	}

	/**
	 * Convert any date to define format
	 *
	 * Date can be timestamp or human readable string
	 *
	 * @param int|string $date
	 * @param bool $isEmpty, check is empty string or 0000-00-00
	 *
	 * @return string
	 */
	public static function date($date, $dateFormat = '', $isEmpty = false) {
		if ($isEmpty && (empty($date) OR $date == '0000-00-00'))
			return '';

		$setting = self::$settings['date'];
		$timestamp = self::getTimestamp($date);

		$settingFormat = ($dateFormat == '') ? $setting['format'] : $dateFormat;
		return date($settingFormat, $timestamp);
	}

	/**
	 * Convert any datetime to define format
	 *
	 * Date can be timestamp or human readable string
	 *
	 * @param int|string $date
	 */
	public static function dateTime($date) {
		$setting['format'] = self::$settings['date']['format'] . ' ' . self::$settings['time']['format'];
		$timestamp = self::getTimestamp($date);
		return date($setting['format'], $timestamp);
	}

	public static function getTimestamp($date) {
		$dateStr = $date;
		if (!self::isTimestamp($date)) {
			$dateStr = strtotime($date);
		}
		return $dateStr;
	}

	/**
	 * Check is timestamp valid
	 *
	 * @param int $timestamp
	 * @return boolean
	 */
	public static function isTimestamp($timestamp) {
		return ((string) (int) $timestamp === $timestamp)
			&& ($timestamp <= PHP_INT_MAX)
			&& ($timestamp >= ~PHP_INT_MAX);
	}

	/**
	 * Date for Data Base
	 *
	 * @param $date
	 * @param string $currentFormat
	 * @return string
	 */
	public static function dateForDb($date, $currentFormat = 'dd/mm/yy')
	{
		if ($date != '' && $date != null && $date != '0000-00-00')
		{
			$split = preg_split('/[dmy]/', $currentFormat, -1, PREG_SPLIT_NO_EMPTY);
			$explodeCurrentFormat = explode($split[0], $currentFormat);
			$explodeDate = explode($split[0], $date);

			if (strpos($explodeCurrentFormat[2], 'y') !== false)
				$explodeDate = array_reverse($explodeDate);

			$implodeDate = implode('-', $explodeDate);

			return date('Y-m-d', strtotime($implodeDate));
		}

		return '0000-00-00';
	}
}