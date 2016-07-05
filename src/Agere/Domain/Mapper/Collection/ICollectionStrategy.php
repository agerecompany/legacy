<?php
namespace Agere\Domain\Mapper\Collection;

use Agere\Domain\Domain;

interface ICollectionStrategy extends \Iterator {
	
	/**
	 * First atribute should be array with id or array with full-data array for create Domain object
	 * 
	 * @param array $raw
	 */
	//public function __construct(array $raw = null);
	
	public function add(Domain $object);
	
	/**
	 * @see \Agere\Domain\Mapper\Traits\TTarget
	 * @todo Wait until ZS will be support traits in full amount
	 */
	//public function targetClass();
	
	/**
	 * Return first element from collection, and delete this element from collection.
	 * 
	 * @return ICollectionStrategy
	 */
	public function shift();
	
	/**
	 * Return last element from collection, and delete this element from collection.
	 * 
	 * @return ICollectionStrategy
	 */
	public function pop();
	
	/**
	 * Count items collection.
	 * 
	 * @return int
	 */
	public function count();
	
	/**
	 * Return object under number
	 * 
	 * @param int $num
	 */
	public function item($num);
	
}