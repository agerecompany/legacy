<?php
/**
 * Special class for working with array
 *
 * @category Agere
 * @package Agere_ArrayUtil
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 10.07.13 18:03
 */

namespace Agere\ArrayUtils;


class ArrayUtils {

	private $array;
	private $_issetKey = false;

	public function __construct(array $array) {
		$this->array = $array;
	}

	/**
	 * Create array object
	 *
	 * @param array $array
	 * @return \Agere\ArrayUtil\ArrayUtil
	 */
	public static function create(array $array) {
		return new self($array);
	}

	/**
	 * Convert array to string view
	 *
	 * This is usefull for eval
	 *
	 * Example:
	 * array(3) {
	 *	  ["real_id"]=>
	 *	  int(40)
	 * 	  ["name"]=>
	 *	  int(40)
	 *	  ["description"]=>
	 *	  int(10)
	 *	}
	 * as string ['real_id' => 40,'name' => 40,'description' => 10];
	 * string(84) "$this->_sphinx->SetFieldWeights(['real_id' => 40,'name' => 40,'description' => 10]);"
	 * @todo Recursively handle and reduce by type
	 */
	public function asString() {
		$arrayAsString = '';
		foreach ($this->array as $key => $value) {
			$arrayAsString .= "'{$key}' => {$value},";
		}
		$arrayAsString = '[' . trim($arrayAsString, ',') . ']';
		return $arrayAsString;
	}

	/**
	 * Array to object
	 *
	 * Recursivly convert array to object
	 *
	 * @param mixed $array
	 * @param string $class
	 * @param bollean $strict
	 * @return unknown|mixed|boolean
	 * @link http://stackoverflow.com/questions/1869091/convert-array-to-object-php#11730039
	 */
	static function arrayToObject($array, $class = 'stdClass', $strict = false) {
		if (!is_array($array)) {
			return $array;
		}

		//create an instance of an class without calling class's constructor
		$object = unserialize(
			sprintf(
				'O:%d:"%s":0:{}', strlen($class), $class
			)
		);

		if (is_array($array) && count($array) > 0) {
			foreach ($array as $name => $value) {
				$name = strtolower(trim($name));
				if (!empty($name)) {

					if(method_exists($object, 'set'.$name)){
						$object->{'set'.$name}(ArrayUtil::arrayToObject($value));
					} else {
						if(($strict)){
							if(property_exists($class, $name)){
								$object->$name = ArrayUtil::arrayToObject($value);
							}
						} else {
							$object->$name = ArrayUtil::arrayToObject($value);
						}
					}
				}
			}
			return $object;
		} else {
			return false;
		}
	}

	/**
	 * Get as close as possible the number of key
	 *
	 * @param $number
	 * @param string $get min | equally | max
	 * @return array|mixed
	 * @throws \Exception
	 * @link http://phpforum.ru/import.phtml?showtopic=62484&hl=&st=0#entry1902334
	 */
	function findMinMaxKey($number, $get = '') {
		$mass = $this->array;
		asort($mass);
		$result = array(
			'min' => array(),
			'max' => array(),
			'equally' => array(),
		);
		foreach ($mass as $mass_key => $mass_value) {
			if ($mass_value < $number) {
				$result['min'][] = $mass_key;
			}
			if ($mass_value == $number) {
				$result['equally'][] = $mass_key;
			}
			if ($mass_value > $number) {
				$result['max'][] = $mass_key;
			}
		}
		//echo '$result["max"] ' . var_dump($result) . '\n';
		if ($get) {
			if ($get == 'equally') {
				$result = array_shift($result['equally']);
			} elseif ($get == 'max') {
				$result = array_shift($result['max']);
			} elseif ($get == 'min') {
				$result = array_pop($result['min']);
			} else {
				throw new \Exception('Not found specifier "' . $get . '"');
			}
		}
		return $result;
	}

	/**
	 * Sort a 2 dimensional array based on 1 or more indexes.
	 *
	 * msort() can be used to sort a rowset like array on one or more
	 * 'headers' (keys in the 2th array).
	 *
	 * @param string|array $key        The index(es) to sort the array on.
	 * @param int          $sort_flags The optional parameter to modify the sorting
	 *                                 behavior. This parameter does not work when
	 *                                 supplying an array in the $key parameter.
	 *
	 * @return array The sorted array.
	 */
	function msort($key, $sort_flags = SORT_REGULAR) {
		if (is_array($this->array) && $this->array) {
			if (!empty($key)) {
				$mapping = array();

				foreach ($this->array as $k => $v) {
					$sort_key = '';

					if (!is_array($key)) {
						$sort_key = $v[$key];
					} else {
						// @TODO This should be fixed, now it will be sorted as string
						foreach ($key as $key_key) {
							$sort_key .= $v[$key_key];
						}

						$sort_flags = SORT_STRING;
					}

					$mapping[$k] = $sort_key;
				}

				asort($mapping, $sort_flags);
				$sorted = array();

				foreach ($mapping as $k => $v) {
					$sorted[] = $this->array[$k];
				}

				return $sorted;
			}
		}

		return $this->array;
	}

	/**
	 * Search key in multidimensional array
	 *
	 * @param string $keyExists - search key
	 * @return bool
	 */
	function msearchKey($keyExists)
	{
		return $this->_recursiveSearchKey($this->array, $keyExists);
	}

	/**
	 * @param $array
	 * @param $keyExists
	 * @return bool
	 */
	private function _recursiveSearchKey($array, $keyExists)
	{
		if (is_array($array))
		{
			if (array_key_exists($keyExists, $array))
			{
				$this->_issetKey = true;
				return;
			}

			foreach ($array as $key => $val)
			{
				if (is_array($val))
					$this->_recursiveSearchKey($val, $keyExists);
			}
		}

		return $this->_issetKey;
	}

}