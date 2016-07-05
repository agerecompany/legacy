<?php
namespace Agere\Memcache;

class Memcache {

	//public static $instance;
	
	private $prefix;
	
	/**
	 * Cache service
	 * 
	 * @var \Memcache
	 */
	private $cache;
	

	public function __construct(\Memcache $memcache, $prefix = '') {
		$this->cache = $memcache;
		$this->prefix = $prefix ? : $_SERVER['HTTP_HOST'];
	}

	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}
	
	public function getPrefix() {
		return $this->prefix;
	}
	
	/*
	 * Wrapper for standart method get
	 */
	public function get($key) {
		return $this->cache->get($this->prefix.$key);
	}
	
	/**
	 * Обгортка для стандартного методу set
	 * Example:
	 * 		$key =  'golovne' . "_" . $this->getLang('id');
	 * 		$this->getCache()->set( $key, $this->golovne, false, 600, array ('golovne', 'block', 'homepage' ) );
	 * @param string $key		Унікальний ключ кешу
	 * @param mixed	$data		Дані котрі добавляєм в кеш
	 * @param string $flag 		Use MEMCACHE_COMPRESSED to store the item compressed (uses zlib). 
	 * @param int $time			Час існування кешу
	 * @param array $tag		Масив блоків до яких відноситься кеш
	 */
	public function set($key, $data, $l = false, $t=0, $tag = null) {
		
		if(!is_null($tag)) {
			$this->addKeyTag($key, $tag);
		}
		$this->cache->set($this->prefix.$key, $data, $l, $t);
	}
		
	public function add($key, $var, $flag = false, $expire = 0, $tag = null ) {
		if(!is_null($tag)) {
			$this->addKeyTag($key, $tag);
		}
		return $this->cache->add($this->prefix.$key, $var, $flag, $expire);
	}
		
	
	
	public function increment($key, $value = 1, $tag = null) {
		if(!is_null($tag)) {
			$this->addKeyTag($key, $tag);
		}		
		return $this->cache->increment($this->prefix.$key, $value);
	}
	
	
	/**
	 * Додаємо теги для ключа
	 */
	protected function addKeyTag($key, $tag = array()) {
		$tags = $this->getTags();
		
		foreach($tag as $t){
			
			$tx = false;
			
			if(isset($tags[$t])){
				
				foreach($tags[$t] as $k){
					if ($k == $key) {
						$tx = true;
						break;
					}
				}
				
			}
			
			if($tx) continue;
			
			$tags[$t][] = $key;
		}
		
		$this->cache->set($this->prefix."_cache_tags", $tags, false, 0);
	}
	
	
	/**
	 * Витягуємо масив тегів
	 */
	public function getTags() {
		return $this->cache->get($this->prefix."_cache_tags");
	}
	
	public function delete($key) {
		return $this->cache->delete($this->prefix.$key);
	}
	
	
	/**
	 * Видаляємо записи по тегах (при передачі декількох тегів, видаляються
	 * лише ті записи, які мають всі перелічені теги)
	 * Наприклад $this->deleteByTag(array('homepage','block')); - вилучить весь кеш
	 * блоків на головні сторінці.
	 * 
	 * @return $i Number deleted elements.
	 */
	public function deleteByTag($tag = array()) {
		if(count($tag) == 0) {
			return false;
		}
		
		$tags = $this->getTags();
		$keys = $tags[$tag[0]];
		
		if(count($tag) > 1) {
			$keys = array_intersect($tags[$tag[0]], $tags[$tag[1]]);
		}
		
		for($j = 2; $j < count($tag); $j++) {
			$keys = array_intersect($keys, $tags[$tag[$j]]);
		}
		
		$i = 0;
		foreach($keys as $key) {
			if($this->delete($key)) $i++;
		}
		
		return $i;
	}
	
	/**
	 * @deprecated	Must be maintain inheritance. This method should be in extension.
	 * @FIXME
	 */
	public function deleteByPrefix($prefix) {
		$tags = $this->cache->get($prefix."_cache_tags");
		
		foreach($tags as $cacheKeys) {
			foreach($cacheKeys as $key) {
				$this->cache->delete($prefix.$key);
			}
		}
		$this->cache->delete($prefix.'_cache_tags');
	}
	
	public function __call($name, $arguments = null) {
		//var_dump($name); die(__METHOD__);
		
		if (!isset($arguments[0])) {
			return $this->cache->{$name}();
		}
		return $this->cache->{$name}($arguments[0]);
	}
	
	public function __set($name, $value) {
		$this->cache->{'set' . ucfirst($name)}($value);
	}	
}