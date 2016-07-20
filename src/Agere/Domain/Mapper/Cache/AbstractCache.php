<?php
namespace Agere\Domain\Mapper\Cache;

use Agere\Memcache\Memcache;

/**
 * Caching data that fetch from DB.
 * Idea give from @link http://alexander.lds.lg.ua/2011/02/%D0%BA%D0%B5%D1%88%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5-%D1%82%D1%8F%D0%B6%D0%B5%D0%BB%D1%8B%D1%85-%D0%B7%D0%B0%D0%BF%D1%80%D0%BE%D1%81%D0%BE%D0%B2-%D0%BD%D0%B0-%D0%BF%D1%80%D0%B8%D0%BC/
 * 
 * To reflect would realise Strategy Pattern
 * 
 * @author Popov Sergiy
 */
abstract class AbstractCache implements ICacheStrategy {

	/**
	 * Memcache object.
	 *
	 * @var Memcache
	 */
	protected $cache;
	
	/**
	 * Client source of data.
	 * 
	 * @var object
	 */
	protected $sourceClient;
		
	/**
	 * Cache prefix key for identify some data.
	 * 
	 * @var string
	 */
	protected $keyPrefix;
	
	/**
	 * Is current environment backend?
	 * 
	 * @var bool
	 */
	protected $isBackend = false;
	
	/**
	 * Cache option.
	 * Allowable key of array: 
	 * 	flag - show strandart memcache param
	 * 	expire - expire time
	 * 	tag - array of tags
	 * 
	 * @var array
	 */
	protected $cacheOptions = array(
		'flag'		=> false,
		'expire'	=> 666, // :)
		'tag'		=> array('somedata')
	);	
	
	/**
	 * Specific data for certain Strategy
	 * @var mixed
	 */
	protected $data;
	
	
	/**
	 * Construct object.
	 * 
	 * @param string $keyPrefix
	 */
	public function __construct($sourceClient, $keyPrefix, $data = null) {
		$this->sourceClient = $sourceClient;
		$this->keyPrefix = $keyPrefix;
		$this->data = $data; // can be specific data for each Strategy
	}
	
	public function execute($findStatement, array $values = array()) {
		// keys for caching
		$cacheKey = $this->cacheKey($findStatement, $values);
		$cacheKeyBackup = $cacheKey . '_backup';
		$cacheKeyLock = $cacheKey . '_lock';
		
		//if(!$this->isBackend()) {
		//	$old = $this->getFromMap($cacheKey);
		//	if($old) { return $old; }
		//}
		
		// time expire and other options
		$options = $this->getCacheOptions();
		$optionsBackup = $optionsLock = $options;
		$optionsBackup['expire'] = 86000;
		$optionsLock['expire'] = 10;
	
		//$data = $this->getFromMap($cacheKeyBackup);
		//$this->setToMap($cacheKey, $data, $options);
		
		//if(($options['expire'] === 0)
		//	|| !($this->getCache() instanceof Memcache) // is fake memcache
		//	|| $this->isBackend()
		//	|| $this->getCache()->add($cacheKeyLock, 1, 0, $optionsLock['expire'])) {
			
			// get new data
			$data = $this->fetch($findStatement, $values);
			//\Agere\ZEngine::dump($data);
			if ($options['expire'] !== 0) { //overwrite standard memcache behavior with expire
				// set the primary and backup caches with acc. lifetime
				$this->setToMap($cacheKey, $data, $options);
				$this->setToMap($cacheKeyBackup, $data, $optionsBackup);
			}
			//$this->getCache()->delete($cacheKeyLock);
		//}
		
		return $data;
	}	
	
	/**
	 * @todo Create global serializator
	 *
	 * @param mixed $findStatement
	 * @param array $values
	 * @return string $cacheKey
	 */
	protected function cacheKey($findStatement, $values) {
		if ($findStatement instanceof \PDOStatement) {
			$findStatement = $findStatement->queryString;
		}
		$cacheKey = $this->keyPrefix . '_' . md5(serialize($findStatement) . serialize($values));

		return $cacheKey;
	}
	
	/**
	 * Depending on the strategy choose which select to do
	 */
	abstract protected function fetch($findStatement, array $values);

    public function getFromMap($cacheKey) {
    	return $this->getCache()->get($cacheKey);
    }
    
	public function setToMap($cacheKey, $data, array $options) {
		$flag = false;
		$expire = 0;
		$tag = null;
		extract($options); // magic
		
	    //$this->getCache()->set($cacheKey, $data, $flag, $expire, $tag);
	}	
	
	public function setCacheOptions($options) {
		if($options) {
			$this->cacheOptions = $options;
		}	
	}
	
	public function getCacheOptions() {
		return $this->cacheOptions;	
	}

	/**
	 * @return Memcache
	 */
	public function getCache() {
		return $this->cache;
	}

	/**
	 * @param Memcache $cache
	 */
	public function setCache($cache) {
		$this->cache = $cache;
	}

	public function setSourceClient($sourceClient) {
		$this->sourceClient = $sourceClient;
	}
	
	public function setIsBackend($isBackend) {
		return $this->isBackend = (bool) $isBackend;
	}
	
	public function isBackend() {
		return $this->isBackend;
	}
	
	public function __sleep() {
		$properties = get_object_vars($this);
		unset($properties['cache']); // this will set automatically
		unset($properties['sourceClient']); // this will set in DeferredCollection
	
		//\Agere\ZEngine::dump($properties);
		//die(__METHOD__ . 'You must delete $cache and $sourceClient');
	
		return array_keys($properties);
	}
	

	public function __wakeup() {
		die('You must implement __wakeup()');
		//$this->setSourceClient(\Agere\Base\App\Registry::getDb()); // not very good. We depend on Db object
	}	
	
}