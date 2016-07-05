<?php
namespace Agere\Domain\Mapper\Cache\Strategy;

use Agere\Domain\Mapper\Cache\AbstractCache;

//use Agere\Domain\Mapper\AbstractMapper;

/**
 * The caching wrapper \Agere\Db over
 * 
 * @author Sergiy Popov
 */
class SqlStrategy extends AbstractCache { 
	
	/**
	 * Method name of \Agere\Db 
	 * 
	 * Example:
	 * 	r, q, arAll, assoc and etc.
	 * 
	 * @var string
	 */
	//private $methodName;
	
	/*
	public function __construct($sourceClient, $keyPrefix, $methodName = null) {
		$this->methodName = $methodName;
		$this->setSourceClient(AbstractMapper::$DB);
		parent::__construct($keyPrefix);
	}*/
	
	protected function fetch($findStatement, array $values = array()) {
		/**
		 * Method name of \Agere\Db
		 *
		 * Example:
		 * 	r, q, arAll, assoc and etc.
		 *
		 * @var string
		 */
		$method = $this->data; // method name in \Agere\Db
		$arrayData = $this->sourceClient->{$method}($findStatement);
		return $arrayData;		
	}

}