<?php
namespace Agere\Domain\Mapper;

use Agere\Domain,
	Agere\Db\Query\Where,
	Agere\Domain\ObjectWatcher\IObjectWatcher;

abstract class DomainObjectFactory {
	
	use \Agere\Domain\Mapper\Traits\TargetTrait;
	
	protected $cacheOption = array(
		'flag'	=> false,
		'expire'=> 500,
		'tag'	=> array('someobject')
	);
	
	/**
	 * ObjectWatcher (UnitOfWork)
	 * 
	 * @var IObjectWatcher
	 */
	protected static $watcher;
	
	/**
	 * 
	 * @var Where
	 */
	protected static $where;
	
	/**
	 * @todo Di for Where
	 */
	public function __construct() {
	}
	
	abstract protected function doCreateObject(array $array);
	
	public function createObject($array) {
		if(is_array($array)) {
			$id = $this->getId($array);
			$old = $this->getFromMap($id);
			//if($old) { return $old; } // @todo use object hash. 0 can be in several object ValidationService::rebuild
			$obj = $this->doCreateObject($array);
			$this->addToMap($obj);
			$obj->markClean();		
			return $obj;
		}
		//@todo write to log
		//throw new Exception\InvalidArgumentException('Expects an array');
	}
	
	protected function getId(&$array) {
		$id = 0;
		if (isset($array['id'])) {
			$id = $array['id'];
			//unset($array['id']);
		}
		
		return (int) $id;
	}
	
	public function getFromMap($key) {
		$watcher = $this->getWatcher();
		return $watcher::exists($this->targetClass(), $key);
	}
	
	/**
	 * Додати об'єкт в кеш.
	 * Можна передавати лише Domain
	 */
	protected function addToMap(Domain\DomainInterface $obj) {
		$watcher = $this->getWatcher();
		return $watcher::add($obj, $this->cacheOption());
	}

	protected function cacheOption() {
		return $this->cacheOption;
	}
	
	/**
	 * @todo this is dupliacte Mapper method, must to be more perfect realization!
	 * @see \Proj\Module\Lang\Model\Lang\Mapper\Traits\TTraits
	 */
	//public function targetClass();
	
	/**
	 * 
	 * @param Where $where
	 */
	public function setWhere(Where $where) {
		self::$where = $where;
	}
	
	/**
	 * If given atribute and it is Where object then return its
	 * otherwise return empty Where object
	 *
	 * @param Where $where
	 * @return Where
	 */
	public function getWhere(Where $where = null) {
		self::$where || self::$where = new Where(); //@FIXME
		return self::$where->clear();
	}	
	
	/**
	 * Set ObjectWatcher (UnitOfWork)
	 * 
	 * System must have only one instance of watcher.
	 * We play safe and allow once set inner static property.
	 * 
	 * @see http://www.ozon.ru/context/detail/id/5648968/
	 * @param IObjectWatcher $watcher
	 * @return \Agere\Domain\Mapper\DomainObjectFactory
	 */
	public function setWatcher(IObjectWatcher $watcher) {
		self::$watcher || self::$watcher = $watcher;
		return $this;
	}
	
	public function getWatcher() {
		return self::$watcher;
	}
	
}