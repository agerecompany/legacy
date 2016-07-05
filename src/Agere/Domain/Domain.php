<?php
namespace Agere\Domain;

use Agere\Domain\Dto\Dto;
use Agere\Domain\Dto\DtoAwareInterface;
use Agere\Domain\Factory\Helper as DomainHelper;
use	Agere\Domain\DomainException;


/**
 * If property equal field in db then we must set level access protected. 
 * But if you create own property you must set level access protected and add doc-comment @personal.
 * 
 * @author Serzh
 */
abstract class Domain implements DomainInterface, DtoAwareInterface {
	
	use \Agere\Domain\SetGetTrait;

	/**
	 * Depth of object convert
	 *
	 * @var int
	 */
	const TO_ARRAY_FULL_DEPTH = 0;
	const TO_ARRAY_FIRST_DEPTH = 1;

	/* protected $id = null;
	
	protected $name;
	
	public function __construct($id = null) {
		if(is_null($id)) {
			//$this->markNew();
		} else {
			$this->id = $id;
		}
	}
	
	public function setId($id){
		if (isset($this->dto)) {
			$this->dto->setId($id);
		}
		$this->id = $id;
	}
	
	public function getId() {
		if (isset($this->dto)) {
			return $this->dto->getId();
		}		
		return $this->id;
	} */
	
	/**
	 * Get object name.
	 * 
	 * @return string $name
	 */
	/* public function getName() {
		return $this->name;
	} */
		
	/**
	 * @var \Agere\Domain\Dto\Dto
	 */
	protected $dto;
	
	public function __construct(Dto $dto = null) {
		if(is_object($dto) && is_null($dto->getId())) {
			//$this->markNew();
		} 
		$this->dto = $dto;
	}
	
	public function getDto() {
		return $this->dto;
	}
	
	public function setDto(Dto $dto) {
		$this->dto = $dto;
		return $this;
	}
	
	public function getId() {
		return $this->dto->getId();
	}
	
	public function getName() {
		return $this->dto->getName();
	}
	
	public function toArray($depth = Domain::TO_ARRAY_FULL_DEPTH) {
		return $this->dto->toArray();
	}
	
	public function setProperties($data) {
		return $this->dto->setProperties($data);
	}
	
	/**
	 * Zf2 compatible
	 * 
	 * @param array $data
	 */
	public function exchangeArray($data) {
		$this->setProperties($data);
	}
	
	/**
	 * Zf2 compatible
	 * 
	 * @return array
	 */
	public function getArrayCopy() {
		return $this->toArray();
	}	
	
	/**
	 * Magic set/get methods to Dto
	 * 
	 * Use only during develop or when performance doesn't have meaning.
	 * For increase perfomance use
	 * $dto = $domainObj->getDto();
	 * $dto->getName();
	 * instead of
	 * $domainObj->getName();
	 * 
	 * @link http://www.garfieldtech.com/blog/magic-benchmarks http://paul-m-jones.com/archives/182
	 */
	public function __call($name, $arguments = null) {
		if (!isset($arguments[0])) {
            if ($name == 'getServiceManager') {
                throw new \Exception('Get Service Manager'); // delete this
            }
			return $this->dto->{$name}();
		}
		return $this->dto->{$name}($arguments[0]);
	}
	
		
	public function markNew() {
		$watcher = $this->watcher();
		$watcher::addNew($this);
	}
	
	public function markDeleted() {
		$watcher = $this->watcher();
		$watcher::addDelete($this);		
	}
	
	public function markDirty() {
		$watcher = $this->watcher();
		$watcher::addDirty($this);
	}
	
	public function markClean() {
		$watcher = $this->watcher();
		$watcher::addClean($this);
	}

	
	// @todo винести ці методи в PersistenceFactory
	public function finder() {
		return self::getFinder(get_class($this));
	}
	
	static function getFinder($type) {
		return DomainHelper::getFinder($type);
	}
	
	public function collection() {
		return self::getCollection(get_class($this));
	}
		
	static function getCollection($type) {
		return DomainHelper::getCollection($type);
	}
	
	/**
	 * Return ObjectWatcher.
	 */
	public function watcher() {
		return self::getWatcher();
	}
		
	static function getWatcher() {
		return DomainHelper::getWatcher();
	}
	
}