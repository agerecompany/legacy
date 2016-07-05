<?php
namespace Agere\Domain\Mapper\Collection;

use Agere\Domain\Mapper\Collection\ICollectionStrategy,
	Agere\Domain\Domain,
	Agere\Domain\Mapper\DomainObjectFactory,
	Agere\Domain\DomainException;

abstract class AbstractCollection implements ICollectionStrategy {
	protected $dofact;
	protected $total = 0;
	protected $raw = array();
	
	protected $pointer = 0;
	protected $objects = array();
	
	public function __construct(array $raw = null, DomainObjectFactory $dofact = null) {
		if(!is_null($raw) && !is_null($dofact)) {
			$this->raw = $raw;
			$this->total = count($raw);
		}
		
		$this->dofact = $dofact;
	}
	
	public function add(Domain $object) {
		$class = $this->targetClass();
		if(!($object instanceof $class)) {
			throw new DomainException("Колекція {$class} не використовується в контексті " . get_class($this));
		}
		$this->notifyAccess();
		$this->objects[$this->total] = $object;
		$this->total++;
	}
	
	//abstract  function targetClass();
	
	protected function notifyAccess() { // this will importance when we use LazeLoad
		// Спеціально залише пустою!
	}
	
	private function getRow($num) {
		$this->notifyAccess();
		if($num >= $this->total || $num < 0) {
			return null;
		}

		if(isset($this->objects[$num])) {
			return $this->objects[$num];
		}

		if(isset($this->raw[$num])) {
			$this->objects[$num] = $this->dofact->createObject($this->raw[$num]);
			return $this->objects[$num];
		}
	}
	
	public function rewind() {
		$this->pointer = 0;
	}
	
	public function current() {
		return $this->getRow($this->pointer);
	}
	
	public function key() {
		return $this->pointer;
	}
	
	public function next() {
		$row = $this->getRow($this->pointer);
		if($row) {
			$this->pointer++;
		}
		return $row;
	}
	
	public function valid() {
		return (!is_null($this->current()));
	}

	/**
	 * Return first element from array, and delete this element from array.
	 * 
	 * @return object
	 */
	public function shift() {
		$object = $this->getRow(0);
		$this->total--;
		array_shift($this->objects);
		array_shift($this->raw);
		return $object;
	}		
	
	/**
	 * Return last element from array, and delete this element from array.
	 * @return object
	 */
	public function pop() {
		$object = $this->getRow($this->total - 1);
		$this->total--;
		array_pop($this->objects);
		array_pop($this->raw);		
		return $object;
	}
	
	public function count() {
		$this->notifyAccess();
		return $this->total;
	}
	
	public function item($row) {
		return $this->getRow($row);
	}

    public function remove($row) {
        $this->total--;
        unset($this->objects[$row]);
        unset($this->raw[$row]);

        $this->objects = array_values($this->objects);
        $this->raw = array_values($this->raw);
        return true;
    }
	
}