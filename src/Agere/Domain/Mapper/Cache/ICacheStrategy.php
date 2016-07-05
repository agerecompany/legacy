<?php
namespace Agere\Domain\Mapper\Cache;

/**
 * Select set id for create Domain object.
 * Idea give from @link http://alexander.lds.lg.ua/2011/02/%D0%BA%D0%B5%D1%88%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5-%D1%82%D1%8F%D0%B6%D0%B5%D0%BB%D1%8B%D1%85-%D0%B7%D0%B0%D0%BF%D1%80%D0%BE%D1%81%D0%BE%D0%B2-%D0%BD%D0%B0-%D0%BF%D1%80%D0%B8%D0%BC/
 * 
 * @author Popov Sergiy
 */
interface ICacheStrategy {
	
	/**
	 * Enter description here ...
	 * 
	 * @param $sourceClient Client to work with the source data. This can be client of SQL, SOAP, XML, etc. 
	 * @param string $keyPrefix Name prefix of cache key
	 * @param int | string $data If you want pass something specific data to your strategy use this attribute
	 */
	public function __construct($sourceClient, $keyPrefix, $data = null);

	/**
	 * Return cache object
	 *
	 * @return mixed
	 */
	public function getCache();

	public function setCache($cache);

	/**
	 * Execute request and add to cache.
	 * 
	 * @param string $findStatement PDO SQL, SOAP, etc. statement
	 * @param array $values Values for execute PDO statement
	 * @return mixed
	 */
	public function execute($findStatement, array $values = array());
	
    /**
     * Get data from cache map by key.
     * 
     * @param string $cacheKey
     * @return mixed
     */
    public function getFromMap($cacheKey);
    
    /**
     * Set data to cache map.
     * 
	 * Allowable key of $options: 
	 * 	flag - show strandard memcache param
	 * 	expire - expire time
	 * 	tag - array of tags
	 * 
     * @param mixed $data
     * @param string $cacheKey
     * @param array $options
     */
	public function setToMap($cacheKey, $data, array $options);
	
	/**
	 * Get option for save cache.
	 * 
	 * @return array
	 */
	public function getCacheOptions();
	
	/**
	 * Set option for save cache.
	 * 
	 * @return boolean
	 */
	public function setCacheOptions($options);
	
	/**
	 * Set source client 
	 * 
	 * This method will be need for serialization.
	 * 
	 * @param mixed $sourceClient Different clients (SOAP, SQL, etc) for get data.
	 */
	public function setSourceClient($sourceClient);
	
	/**
	 * Set whether current environment is backend
	 * 
	 * @param bool $isBackend
	 */
	public function setIsBackend($isBackend);
	
	/**
	 * Is current environment backend?
	 * 
	 * @return bool
	 */
	public function isBackend();
	
}