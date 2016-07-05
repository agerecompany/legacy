<?php
namespace Agere\Domain\Dto;

use Agere\Domain\DomainInterface;
//use Agere\Domain\DomainException;

/**
 * @author Serzh
 */
abstract class Dto implements DomainInterface {
	
	use \Agere\Domain\SetGetTrait;
	
	protected $id = null;
	
	
	public function __construct($id = null) {
		$this->id = $id;
	}
	
	public function setId($id){
		$this->id = $id;
	}
	
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Get object name
	 * 
	 * This can return name, title, subname or something else that contain short text information
	 * 
	 * @return string
	 */
	abstract function getName();

	public function toArray() {
		return get_object_vars($this);
	}
	
}