<?php
namespace Agere\Domain\Mapper\Cache;
use Agere\Domain\Mapper\Cache\ICacheStrategy;

class CacheContext {
	
	/**
	 * Strategy object
	 * 
	 * @var ICacheStrategy
	 */
	private $instance;
	
	public function __construct(ICacheStrategy $instance) {
		$this->instance = $instance;
	}
	
	public function execute($findStatement, array $values = array()) {
		return $this->instance->execute($findStatement, $values);
	}

	public function getCache() {
		return $this->instance->getCache();
	}

	public function setCache($cache) {
		return $this->instance->setCache($cache);
	}

	public function getFromMap($cacheKey) {
		return $this->instance->getFromMap($cacheKey);
	}
	
	public function setToMap($cacheKey, $data, array $options) {
		return $this->instance->setToMap($cacheKey, $data, $options);
	}
	
	public function getCacheOptions() {
		return $this->instance->cacheOptions();
	}
	
	public function setCacheOptions($options) {
		return $this->instance->setCacheOptions($options);
	}
	
	public function setSourceClient($sourceClient) {
		return $this->instance->setSourceClient($sourceClient);
	}
	
	public function setIsBackend($isBackend) {
		return $this->instance->setIsBackend($isBackend);
	}
	
	public function isBackend() {
		return $this->instance->isBackend();
	}
	
}