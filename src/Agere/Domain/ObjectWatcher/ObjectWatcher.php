<?php
namespace Agere\Domain\ObjectWatcher;

use Agere\Domain,
	Agere\Memcache\Memcache;

class ObjectWatcher implements IObjectWatcher {
	
	protected $all = array();
	protected $dirty = array();
	protected $new = array();
	protected $delete = array();
	
	protected static $instance;

	protected $cache = null;

	/**
	 * @todo Create Memcache interface
	 * @param Memcache $memcache
	 */
	public function __construct($memcache) {
		//$this->cache = MemcacheAirweb::getInstance();
		$this->cache = $memcache;
		static::$instance = $this;
	}

	public function getCache() {
		return $this->cache;
	}

	/**
	 * @return \Agere\Domain\ObjectWatcher\ObjectWatcher
	 */
	static function getInstance() {
		/*if(!isset(static::$instance)) {
			static::$instance = new static();
		}*/
		return static::$instance;
	}
	
	public static function key($classname, $id) {
		return $classname . '.' . $id;
	}
	
	public function globalKey(Domain\DomainInterface $obj) {
		$key = static::key(get_class($obj), $obj->getId());
		return $key;
	}
	
	static function set(Domain\DomainInterface $obj) {
		$inst = static::getInstance();
		$inst->all[$inst->globalKey($obj)] = $obj;
	}
	
	static function add(Domain\DomainInterface $obj) {
		$inst = static::getInstance();
		if ($inst->exists(get_class($obj), $obj->getId())) {
			return false;
		}
		$inst->all[$inst->globalKey($obj)] = $obj;
		return true;
	}
	
	static function exists($classname, $id) {
		$inst = static::getInstance();
		$key = static::key($classname, $id);
		if(isset($inst->all[$key])) {
			return $inst->all[$key];
		}
		return null;
	}
	
	static function delete($classname, $id) {
		$inst = static::getInstance();
		$_key = static::key($classname, $id);
		unset($inst->all[$_key]);
		return true;
	}	
	
	static function addDirty(Domain\DomainInterface $obj) {
		$inst = static::getInstance();
		if(!in_array($obj, $inst->new, true)) {
			$inst->dirty[$inst->globalKey($obj)] = $obj;
		}
	}

	static function addNew(Domain\DomainInterface $obj) {
		$inst = static::getInstance();
		$inst->new[] = $obj;
	}

	static function addClean(Domain\DomainInterface $obj) {
		$static = static::getInstance();
		unset($static->dirty[$static->globalKey($obj)]);
		
		if(in_array($obj, $static->new, true)) {
			$pruned = array();
			foreach($static->new as $newobj) {
				if(!($newobj === $obj)) {
					$pruned[] = $newobj;
				}
			}
			$static->new = $pruned;
		}
	}

	public function performOperation() {
		/**
		 * @var \Agere\Domain\Domain $obj
		 */
		foreach($this->dirty as $key => $obj) {
			$obj->finder()->save($obj); // update
		}
		foreach($this->new as $key => $obj) {
			$obj->finder()->save($obj); // insert
		}
		$this->dirty = array();
		$this->new = array();
	}
}