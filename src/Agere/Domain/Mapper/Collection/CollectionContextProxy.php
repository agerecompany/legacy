<?php
/**
 * The Class contain the Strategy and Proxy patterns
 * 
 * This is Context.
 */

namespace Agere\Domain\Mapper\Сollection;

use Agere\Domain\Mapper\Collection\ICollectionStrategy;

/**
 * This class is wrapper about Strategy.
 * 
 * Note: To my mind this class may implement IMapperStrategy.
 * In some case this class similar to Proxy pattern.
 * 
 * @link http://inroot.ru/2010-07/pattern-strategy.html
 * @author Popov Sergiy
 */
class CollectionContextProxy implements ICollectionStrategy {

	/**
	 * Instance of collection.
	 * 
	 * @var ICollectionStrategy
	 */
	private $_collection;
	
	public function __construct(ICollectionStrategy $collection) {
		$this->_collection = $collection;
	}
	
	public function add(Domain $object) {
		return $this->_collection->add($object);
	}
	
	public function targetClass() {
		return $this->_collection->targetClass();
	}
	
	public function current() {
		return $this->_collection->current();
	}
	
	public function rewind() {
		return $this->_collection->rewide();
	}
	
	public function key() {
		return $this->_collection->key();
	}
	
	public function next() {
		return $this->_collection->next();	
	}
	
	public function valid() {
		return $this->_collection->valid();
	}
	
	/**
	 * Return first element from collection, and delete this element from collection.
	 * 
	 * @return ICollectionStrategy
	 */
	public function shift() {
		return $this->_collection->shift();
	}
	
	/**
	 * Return last element from collection, and delete this element from collection.
	 * 
	 * @return ICollectionStrategy
	 */
	public function pop() {
		return $this->_collection->pop();
	}
	
	/**
	 * Count items collection.
	 * 
	 * @return int
	 */
	public function count() {
		return $this->_collection->count();
	}
	
	public function item($num) {
		return $this->_collection->item($num);
	}
	
	
	
}