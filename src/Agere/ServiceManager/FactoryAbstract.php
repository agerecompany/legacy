<?php
/**
 * Enter description here...
 *
 * @category Agere
 * @package Agere_<package>
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 25.07.13 9:24
 */

namespace Agere\ServiceManager;

use Agere\ServiceManager\Exception\InvalidArgumentException;

abstract class FactoryAbstract {

	/**
	 * Map for mini Service Locator of Domain package
	 *
	 * @var array
	 */
	protected static $mapObject = array();

	/**
	 * Core object for create other object.
	 *
	 * Can be db, cache, mail, etc objects.
	 *
	 * @var array
	 */
	protected static $maintenance = array();


	/**
	 * Конструктор буде закритим, поки не виясню чи потрібний мені взагалі екземпляр цього класу
	 */
	private function __construct() {}

	public static function addMaintenance($key, $object) {
		self::$maintenance[$key] = $object;
	}

	/**
	 * Get maintenance (core) object for create other object
	 *
	 * @param string $key
	 * @return mixed
	 * @throws InvalidArgumentException
	 */
	public static function getMaintenance($key) {
		if (!isset(self::$maintenance[$key])) {
			throw new InvalidArgumentException(sprintf('Cannot find maintenance object by key "%s". You must set a object with this key in your Application Bootstrap Module.', $key));
		}
		return self::$maintenance[$key];
	}

	static protected function getInstance($objectName, $dependence = null) {
		if (!isset(self::$mapObject[$objectName])) {

            if (class_exists($objectName)) {
                if ($dependence === null) {
                    self::$mapObject[$objectName] = new $objectName();
                } else {
                    self::$mapObject[$objectName] = new $objectName($dependence);
                }
            } else {
                throw new InvalidArgumentException("Mapper class {$objectName} hasn't found. Please, check format parameter in method " . __METHOD__);
            }
		}
		return self::$mapObject[$objectName];
	}

}