<?php
namespace Agere\Domain\Mapper\Collection;

use Agere\Domain\Mapper\Collection\AbstractCollection,
	Agere\Domain\Mapper\Cache\CacheContext,
	Agere\Domain\Mapper\DomainObjectFactory;

abstract class DeferredCollection extends AbstractCollection {
	
	/**
	 * Fetcher information about collection
	 * 
	 * @var CacheContext
	 */
	private $fetcher;
	
	/**
	 * Sql string for PDOStatement
	 * @string
	 */
	private $stmt;
	
	/**
	 * Values for PDOStatement
	 * @var array
	 */
	private $valueArray;
	
	/**
	 * Indicator launch
	 * 
	 * @var boolean
	 */
	private $run = false;
	
	public function __construct(CacheContext $fetcher, 
		DomainObjectFactory $factory,
		//\PDOStatement $statement,
		$sqlStatement,
		array $valueArray
	) {
		parent::__construct(null, $factory);
		$this->fetcher = $fetcher;
		$this->stmt = $sqlStatement;
		$this->valueArray = $valueArray;
	}
	
	public function notifyAccess() {
		if (! $this->run) {
			$this->raw = $this->fetcher->execute($this->stmt, $this->valueArray);
			//\Agere\ZEngine::dump($this->raw);
			$this->total = count($this->raw);
		}
		$this->run = true;
	}
	
	/*
	public function __sleep() {
		$properties = get_object_vars($this);
		unset($properties['fetcher']);
		//\Agere\ZEngine::dump(array_keys($properties));
		//die(__METHOD__ . 'You must delete $fetcher');
		
		return array_keys($properties);
	}
	*/
	
}