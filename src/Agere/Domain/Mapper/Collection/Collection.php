<?php
namespace Agere\Domain\Mapper\Collection;

use Agere\Domain\Domain,
	Agere\Domain\DomainException;

abstract class Collection implements \Iterator {
	
	protected $dofact;
	protected $total = 0;
	//protected $raw = array();
	
	private $result;
	private $pointer = 0;
	private $objects = array();
	
	
	//public function __construct(array $raw = null, Mapper_Abstract $dofact = null) {
	public function __construct(array $raw = null) {
		if(!is_null($raw)) {
			//$this->raw = $raw;
			
			$this->objects = $raw;
			$this->total = count($raw);
		}
		
		//$this->dofact = $dofact;
	}
	
	public function add(Domain $object) {
		$class = $this->targetClass();
		if(!($object instanceof $class)) {
			throw new DomainException("Це колекція {$class}. Вона не використовується в контексті " . get_class($this));
		}
		$this->notifyAccess();
		$this->objects[$this->total] = $object;
		$this->total++;
	}
	
	abstract function targetClass();
	
	protected function notifyAccess() {
		// спеціально залишено пустою
	}
	
	private function getRow($num) {
		$this->notifyAccess();
		
		if($num >= $this->total || $num < 0) {
			return null;
		}
		
		if(isset($this->objects[$num])) {
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

	public function shift() {
		$this->total--;
		return array_shift($this->objects);
	}		
	
	public function pop() {
		$this->total--;
		return array_pop($this->objects);
	}
	
	public function count() {
		return $this->total;
	}
	
}