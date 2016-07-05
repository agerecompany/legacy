<?php
/**
 * Advanced string processing
 *
 * @category Agere
 * @package Agere_String
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 15.07.13 15:30
 */

namespace Agere\String;


class String {

	/**
	 * Simple string
	 *
	 * @var string
	 */
	private $_string;

	public function __construct($string) {
		$this->_string = $string;
	}

	public static function create($string) {
		return new self($string);
	}

	/**
	 * Return the number of words in a string
	 *
	 * @return int number
	 */
	public function countWords() {
		$string = $this->_string;

		$string= str_replace("&#039;", "'", $string);
		$t = array(' ', "\t", '=', '+', '-', '*', '/', '\\', ',', '.', ';', ':', '[', ']', '{', '}', '(', ')', '<', '>', '&', '%', '$', '@', '#', '^', '!', '?', '~'); // separators
		$string= str_replace($t, " ", $string);
		$string= trim(preg_replace("/\s+/", " ", $string));
		$num = 0;
		if ($this->strlen() > 0) {
			$word_array = explode(" ", $string);
			$num = count($word_array);
		}
		return $num;
	}

	public function strtolower() {
		$string = mb_strtolower($this->_string, "UTF-8");
		return new self($string);
	}

	public function strlen() {
		// Return mb_strlen with encoding UTF-8.
		return mb_strlen($this->_string, "UTF-8");
	}

	/**
	 * @return string
	 * @link http://oleg.in-da.ru/dev/php/funkcija_ucfirst_i_kirilica_v_kodirovke_utf-8
	 */
	public function ucfirst() {
		$string = $this->_string;
		$string = mb_strtoupper(mb_substr($string, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($string, 1, mb_strlen($string), 'UTF-8');
		return new self($string);
	}

	public function stripTags($allowableTags = '') {
		$string = strip_tags($this->_string, $allowableTags);
		return new self($string);
	}

	/**
	 * This truncates a variable to a character length, the default is 80.
	 * As an optional second parameter, you can specify a string of text to display at the end if the variable was truncated.
	 * The characters in the string are included with the original truncation length.
	 * By default, truncate will attempt to cut off at a word boundary.
	 * If you want to cut off at the exact character length, pass the optional third parameter of TRUE.
	 *
	 * @param $length Modify for desired width
	 * @param $replace This is a text string that replaces the truncated text. Its length is included in the truncation length setting
	 * @return $this
	 */
	public function truncate($length = 150, $replace = '') {
		if ($this->strlen() <= $length) {
			$string = $this->_string; //do nothing
		} else {
			//\Rotor\ZEngine::dump(wordwrap($this->_string, $length));
			//$string = mb_substr($this->_string, 0, mb_strpos(wordwrap($this->_string, $length, ' '), ' '));
			//$string = mb_substr($this->_string, 0, mb_strrpos(wordwrap($this->_string, $length, ' '), ' ', -1, 'UTF-8'));

			$string = mb_substr($this->_string, 0, mb_strpos($this->_string, ' ', $length));
		}
		return new self($string . $replace);
	}

	/**
	 *
	 * param string $str The text string to split
	 * param integer $words The number of words to extract. Defaults to 15
	 */
	public function truncateWord($words = 15) {
		$arr = preg_split("/[\s]+/",  $this->_string, $words + 1);
		$arr = array_slice($arr, 0, $words);
		$string = join(' ', $arr);
		return new self($string);
	}

	/**
	 * Advanced sprintf process pattern with string key
	 *
	 * Can replace by %key% pattern
	 *
	 * @param $vars
	 * @param string $char
	 * @return mixed
	 * @link http://www.php.net/manual/en/function.sprintf.php#83779
	 */
	public function sprintfKey($vars, $char = '%') {
		$str = $this->_string;
		$tmp = array();
		foreach($vars as $k => $v) {
			$tmp[$char . $k . $char] = $v;
		}
		$string = str_replace(array_keys($tmp), array_values($tmp), $str);
		return new self($string);
	}

	public function replace($search, $replace) {
		$string = str_replace($search, $replace, $this->_string);
		return new self($string);
	}

	public function toCamelCase() {
		$parts = explode('_', $this->_string);

		$string = '';
		foreach ($parts as $part) {
			$string .= ucfirst($part);
		}
		return new self($string);
	}

	public function __toString() {
		return $this->_string;
	}

	public function toString() {
		return $this->__toString();
	}

}