<?php
namespace Agere\Domain\ObjectWatcher\Cache;

use Agere\Domain\ObjectWatcher\ObjectWatcher,
	Agere\Domain;

class MemcacheObjectWatcher extends ObjectWatcher {

	/**
	 * Memcache object.
	 * 
	 * @var Memcache
	 */
	//private $cache;

	/*static function getInstance() {
		if(!isset(static::$instance)) {
			static::$instance = new static();
		}
		return static::$instance;
	}*/
	

	
	/**
	 * @param Domain\DomainInterface $obj
	 * @param array|false $options
	 */
	static function set(Domain\DomainInterface $obj, $options = array()) {
		parent::set($obj);
		
		$flag = false;
		$expire = 0;
		$tag = null; 
		extract($options); // magic
		
		$inst = static::getInstance();
		$globalKey = $inst->globalKey($obj);
		$inst->cache->set($globalKey, $obj, $flag, $expire, $tag);
	}
	
	
	/**
	 * @param Domain $obj
	 * @param array $options
	 */
	static function add(Domain\DomainInterface $obj, $options = false) {
		parent::add($obj);
		
		$flag = false;
		$expire = 0;
		$tag = null; 
		extract($options); // magic
		
		$inst = static::getInstance();
		$globalKey = $inst->globalKey($obj);
		return $inst->cache->add($globalKey, $obj, $flag, $expire, $tag);
	}
	
	/**
	 * @todo refacoring exclute parent code to this method
	 */
	static function exists($classname, $id) {
		$cache = parent::exists($classname, $id);
		if($cache) {
			return $cache;
		}
		
		$inst = static::getInstance();
		$key = static::key($classname, $id);
		return $inst->cache->get($key);
	}

	/**
	 * @todo this method in ObjectWatcher
	 * @param string $classname
	 * @param string $id
	 */
	static function delete($classname, $id) {
		parent::delete($classname, $id);
		$inst = static::getInstance();
		$_key = static::key($classname, $id);
		$result = $inst->cache->delete($_key);
		return $result;
	}	
}