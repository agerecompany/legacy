<?php
namespace Agere\Domain\Factory;

use Agere\Memcache\Memcache;
use Agere\ServiceManager\FactoryAbstract;
//use Agere\Domain\DomainException;

/**
 * AbstractFactory Pattern
 * 
 * @link http://bitbybit.ru/article/218
 * @author Sergiy Popov
 */
class Helper extends FactoryAbstract {

	/**
	 * Можливо більш співзвучніша назва getFactory, але залишу так як в книзі,
	 * на відпочинку потрібно буде порефакторити. 
	 * @todo
	 * 
	 * В атрибуті отримуєм назву класу Domain (Модель предметної області) 
	 * екземпляр фабрики(!) котрого нам потрібно отримати.
	 * "Domain\Article" => "Domain\Mapper\Factory\Persistence\Article"
	 * 
	 * Якщо нам потрібно буде реалізувати шаблон Separated Interface,
	 * метод getFinder() повертав би екземпляр інтерфейсу Finder, 
	 * а наші об'єкти Mapper реалізовували б це. Але в даному випадку залишем це для рефакторингу.
	 * В даному випадку getFinder() просто повертає об'єкт Mapper. (стр. 300) 
	 * 
	 * @param string $typestr -> "\Magere\Model\News\News"
	 */
	static public function getFinder($typestr) {
		static $db = null;

		if ($db === null) {
			$db = static::getMaintenance('Agere\Db');
		}

		$type = static::handleType($typestr);
		$factory = "\\Magere\\{$type['module']}\\Model\\{$type['model']}\\Mapper\\{$type['name']}Mapper";
		//$factory = sprintf('\Magere\%s\Model\%\Mapper\%sMapper', $type['module'], $type['model'], $type['name']);

		return static::getInstance($factory, $db);
	}	
	
	static public function getCollection($typestr) {
		$type = static::handleType($typestr);
		$collection = "\\Magere\\{$type['module']}\\Model\\{$type['model']}\\Mapper\\{$type['name']}Collection";
		
		return static::getInstance($collection);
	}

	/**
	 * Get DomainObjectFactory for create Domain object.
	 *
	 * @param string $typestr Name of collection
	 * @return \Agere\Domain\Mapper\DomainObjectFactory
	 */
	static public function getObjectFacroty($typestr) {
		$type = static::handleType($typestr);
		$objectFactory = "\\Magere\\{$type['module']}\\Model\\{$type['model']}\\Mapper\\{$type['name']}ObjectFactory";

		return static::getInstance($objectFactory);
	}
	
	/**
	 * Get ObjectWatcher
	 */
	static public function getWatcher() {
		/**
		 * @var \Agere\Memcache\Memcache $memcache
		 */
		static $memcache = null;

		if ($memcache === null) {
			//$memcache = static::getMaintenance('Agere\Memcache');
		}

		//$memcacheEnabled = \Agere\Base\App\Registry::getConfig()->get('cache.memcache.status') ;
		$memcacheEnabled = false;
		
		if($memcacheEnabled) {
			//return \Agere\Domain\ObjectWatcher\Cache\MemcacheObjectWatcher::getInstance();
			return static::getInstance('\\Agere\\Domain\\ObjectWatcher\\Cache\\MemcacheObjectWatcher', $memcache);
		}
		return static::getInstance('\\Agere\\Domain\\ObjectWatcher\\ObjectWatcher', $memcache);
	}

	/**
	 * Обробляєм назву отриманого параметру. 
	 * Розбиваєм назву на частини і засовуєм в масив.
	 * Повертаєм масив з типом і моделлю.
	 * 
	 * @return array $type
	 */
	static function handleType($typestr) {
        //$callers=debug_backtrace();
       // echo $callers[1]['class'] . ':' . $callers[1]['function'] . '<br />';

		$typestr = trim(trim(str_replace('\\', '/', $typestr), '/'));
		$typeArr = explode('/', $typestr);
		
		$type = array();
		$number = count($typeArr);
		// 
		if ($number > 2) { // standard call "Magere\Lang\Model\Lang\Lang"
			$type['module'] = ucfirst($typeArr[1]);
			$type['name'] = ucfirst(array_pop($typeArr));
			$type['model'] = ucfirst(array_pop($typeArr));
		} elseif ($number == 2) { // short call "lang/lang"
			$type['module'] = ucfirst(array_shift($typeArr));
			$type['name'] = $type['model'] = ucfirst(array_pop($typeArr));
		} else {  // mini-short call "lang"
			$type['module'] = $type['name'] = $type['model'] = ucfirst($typeArr[0]);
		}
		return $type;
	}

}