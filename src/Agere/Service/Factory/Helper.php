<?php
namespace Agere\Service\Factory;

use Agere\ServiceManager\FactoryAbstract;

class Helper extends FactoryAbstract {
	
	/**
	 * Create services factory
	 *
	 * @return
	 */
	public static function create($typestr) {
		$type = self::handleType($typestr);
		$service = "\\Magere\\{$type['module']}\\Service\\" . $type['service'] . 'Service';

		return static::getInstance($service);
	}

	/**
	 * @param string $typestr
	 * @return array $pathPart
	 */
	public static function handleType($typestr) {
		$typestr = trim($typestr);
		$explode = explode('/', $typestr);
		
		$type = array();
		foreach($explode as $part) {
			$type[] = ucfirst($part);
		}
		
		$pathPart = [];
		$pathPart['module'] = $type[0];
		$pathPart['service'] = $type[0];
		if (count($type) > 1) {
			$pathPart['module'] = array_shift($type);
			$pathPart['service'] = implode('\\', $type);
		}
		return $pathPart;
	}

}