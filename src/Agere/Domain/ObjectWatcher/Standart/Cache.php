<?php
namespace Agere\Domain\ObjectWatcher;

use Agere\MemcacheAirweb;

class Cache {
	private $all = array();
	private $dirty = array();
	private $new = array();
	private $delete = array();
	
	private static $instance;
	
	/**
	 * РћР±С”РєС‚ РґР»СЏ СЂРѕР±РѕС‚Рё Р· РєРµС€РѕРј (Memcache).
	 * Р¦Рµ РґР°С” Р·РјРѕРіСѓ РєРѕРЅС‚СЂРѕР»СЋРІР°С‚Рё РѕР±С”РєС‚Рё РіР»РѕР±Р°Р»СЊРЅРѕ, 
	 * Р° РЅРµ Р»РёС€Рµ РЅР° С‡Р°СЃ РІРёРєРѕРЅР°РЅРЅСЏ РѕРґРЅРѕРіРѕ Р·Р°РїРёС‚Сѓ.
	 * @var object
	 */
	private $cache;
	
	private function __construct() {}
	
	static function getInstance() {
		if(!isset(self::$instance)) {
			self::$instance = new self();
			self::$instance->cache = MemcacheAirweb::getInstance();
		}
		return self::$instance;
	}
	
	public function globalKey($obj) {
		// take class name from full namespace
		$parentsNamespace = class_parents($obj);  
		$parentsClasses = explode('\\', array_pop($parentsNamespace));
		//$parentClass = array_pop($parentsClasses);  
		$firstParentClass = array_pop($parentsClasses);
		//$firstParentClass = implode('', $parentsClasses);  
		//$firstParentClass = str_replace('\\', '', $parentClass); \ZEngine::dump($parentsClasses);
		$method = 'globalKey' . $firstParentClass; 
		
		$key = $this->$method($obj);
		return $key;
	}
	
	public function globalKeyDomain(\Agere\Domain\Domain $obj) {
		$key = get_class($obj) . "." . $obj->getId();
		return $key;
	}
	
	//public function globalKeyDomainMapperCollection(\Agere\Domain\Mapper\Collection $obj) {
	public function globalKeyCollection(\Agere\Domain\Mapper\Collection $obj) {
		$key = get_class($obj) . ".";
		return $key;
	}
	
	/**
	 * Р”РѕРґР°С‚Рё РѕР±'С”РєС‚ РІ РєРµС€
	 * @param Domain | Mapper_Collection $obj
	 * @param array $options
	 */
	static function add($obj, $options = false, $additionalKey = false) {
		$inst = self::getInstance();
		$flag = false;
		$expire = 0;
		$tag = null; 
		extract($options); // magic
		
		//$inst->all[$inst->globalKey($obj)] = $obj;
		$globalKey = $inst->globalKey($obj) . $additionalKey;
		
		//ZEngine::dump($globalKey);
		//die();
				
		//$globalKey = $inst->globalKey($obj);
		$inst->cache->set( $globalKey, $obj, $flag, $expire, $tag );
	}
	
	static function exists($classname, $key) {
		$inst = self::getInstance();
		$_key = "$classname.$key";

		if($inst->cache->get($_key)) {
			return $inst->cache->get($_key);
		}
		return null;
	}
	
	
	static function delete($classname, $key) {
		$inst = self::getInstance();
		$_key = "$classname.$key";
		return $inst->cache->delete($_key);
	}
}