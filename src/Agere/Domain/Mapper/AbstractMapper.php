<?php
/**
 * Pattern Data Mapper
 * @author Popov S.
 */
namespace Agere\Domain\Mapper;

use Agere\Db\Db,
	Agere\Domain\Domain,
	Agere\Db\Query\Where,
	Agere\Domain\Mapper\Cache\CacheContext,
	Agere\Domain\Mapper\Cache\Strategy\SqlStrategy,
	Agere\Domain\Mapper\Cache\Strategy\PdoStrategy,
	Agere\Domain\Mapper\Collection\AbstractCollection;

abstract class AbstractMapper implements IMapperStrategy {
	
	use \Agere\Domain\Mapper\Traits\TargetTrait;

	public static $db = null;
	
	/**
	 * Switch that init when call one of method select
	 * @var string
	 */
	//protected $target;
	
	/**
	 * Multidimentional array with options for data and collections
	 * 
	 * Attention!!! 
	 * You mustn't use this for Domain Object.
	 * @see DomainObjectFactory
	 * 
	 * @see Cache\AbstractCache
	 * @var array
	 */
	protected $cacheOptions = array(
		'collection' => array(
			'flag'		=> false,
			'expire'	=> 60,
			'tag'		=> array('somecollection')
		)
	);
	
	/**
	 * Object for create object.
	 * 
	 * @var \Agere\Domain\Mapper\DomainObjectFactory
	 */
	protected $dofact;
	
	/**
	 * Source name
	 */
	protected $docTable = '';

	/**
	 * Основний аліас таблиці.
	 * For example:
	 * 		В таблиці doctype_news основний аліас 'dno'
	 * 
	 * @var string
	 */
	protected $alias;
	
	/**
	 * Кількість сутностей на сторінці
	 * var int
	 */
	protected $limit = 36;

	/**
	 * З якого набору починати показ
	 * @var int
	 */
	protected $offset = 0;	
	
	/**
	 * Масив значення для сортування запиту.
	 * @see Mapper_Abstract::orderBy()
	 * @var array
	 */
	protected $orderBy = array();
	
	/**
	 * Масив значення для групування
	 * 
	 * @var array
	 */
	protected $groupBy = array();

	/**
	 * Обєкт Query_Query для роботи з побудовую умови WHERE для запиту
	 * @var object
	 */
	protected $condition;
	
	/**
	 * Умова LIMIT для запиту
	 * @var string
	 */
	protected $_limit;	
	
	/**
	 * Switch for change statement query
	 * 
	 * Assume method name of query statment.
	 * 
	 * @var string
	 */
	protected $_switch = 'countStatement';

	
	public function __construct(Db $db) {
		self::$db || self::$db = $db;
	}

	/**
	 * SQL query carcass for select one item.
	 * @return $carcass 	
	 */
	abstract protected function selectStmt();
	
	/**
	 * SQL query carcass for select collection item.
	 * @return $carcass 	
	 */	
	abstract protected function findStatement();
	
	/**
	 * SQL query carcass for count item.
	 * @return $carcass 	
	 */		
	abstract protected function countStatement();
	
	/**
	 * Target class name object which will create during select.
	 */
	//abstract protected function targetClass() {} //@FIXME this must be abstract but for compatibility with ZS9 about use traits I delete abstract key
	
	abstract protected function doDelete($id);

	/**
	 * Implement this method if need save complex domain object
	 *
	 * @param Domain $obj
	 * @return mixed
	 */
	abstract protected function doSave(Domain $obj);

	protected function doSaveCollection(AbstractCollection $collection) {
	}

	public function findBy($data) {}
	
	public function getDb() {
		if (self::$db) {
			return self::$db;
		}
		throw new MapperException('\Agere\Db object doesn\'t set in ' . __METHOD__ . ". You must only once give db object in AbstractMapper::__construct or AbstractMapper::setDb()");
	}
	
	public function setDb(Db $db) {
		self::$db = $db;
	}
	
	/**
	 * Create Domain object.
	 * 
	 * @param array $array
	 * @return \Agere\Domain\Domain $object
	 */
	//abstract protected function doCreateObject($array);
	//abstract function getCollection();
	
	public function find($id) {
		//$this->target = 'object';
		// Перевіряєм чи не витягувалось це раніше
		//$old = $this->getFromMap($id);
		$dofact = $this->getObjectFactory();
		$old = $dofact->getFromMap($id);
		
		if($old) { return $old; }
		
		$findStatement = null;
		
		try {
			$findStatement = self::$db->prepare($this->selectStmt());
			
			//\Agere\ZEngine::dump( $findStatement );
		  	//\Agere\ZEngine::dump( $id );
		  	
			$findStatement->execute(array($id));
			//$rs->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->targetClass());
			//$findStatement->setFetchMode(\PDO::FETCH_CLASS, $this->targetClass());
			$findStatement->setFetchMode(\PDO::FETCH_ASSOC);
			$array = $findStatement->fetch();
			$findStatement->closeCursor();
			
			if(!$array) { return false; }
			
			if($this->selectStmt() === 'SELECT * FROM `store` AS `s` WHERE id = ?') {
				unset($array['car_type_vehicle_id']);
			}
// 			var_dump($dofact->createObject($array)); die;
			$object = $dofact->createObject($array);
			//$this->addToMap($object);
			return $object;
		} catch (\PDOException $e) {
			throw new \PDOException($e); 
		}
	}

	public function findWhere(Where $where) {
		$this->setCondition($where);
		
		$findStatement = $this->findStatement();
		//$findStatement = self::$DB->prepare($this->findStatement());
		$values = $where->getTemplate();
		
		//\Agere\Debug::dump($findStatement);
		//\Agere\ZEngine::dump(self::$DB);
		
		try {
			$collectionName = $this->targetClassCollection();
			$fetcher = new CacheContext(new PdoStrategy(self::$db, $collectionName, 'fetchAll'));
			$fetcher->setCache($this->getWatcher()->getCache());
			$fetcher->setCacheOptions($this->cacheOptions('collection'));
			$fetcher->setIsBackend(true); // @todo $module == 'admin' @FIXME

			//$idsArray = $fetcher->executeSet($findStatement, $values);
			//\Agere\ZEngine::dump($idsArray);
			//$collection = new $collectionName($idsArray, $this->getObjectFactory());

            //$this->getCollectionFactory();

			$collection = new $collectionName($fetcher, $this->getObjectFactory(), $findStatement, $values); // @todo factory method @link http://andrey.moveax.ru/patterns/oop/structural/proxy/
			return $collection;  
		} catch (\PDOException $e) {
			throw new \PDOException($e); 
		}
    }		
    
    /**
     * Find some data
     *
     * @param Where $where
     * @param \Closure $method Method name in \Agere\Db\Db
     * @return mixed
     */
    protected function findData(Where $where, \Closure $statement) {
    	$this->setCondition($where);

    	try {
			$values = $where->getTemplate();

			//\Agere\Debug::dump($statement());
			//\Agere\Debug::dump($values); die(__METHOD__);
		
			//$collectionName = $this->targetClassCollection();
			$fetcher = new CacheContext(new PdoStrategy(self::$db, 'Data', 'fetchAll'));
			$fetcher->setCache($this->getWatcher()->getCache());
			$fetcher->setCacheOptions($this->cacheOptions('collection'));
    		$data = $fetcher->execute($statement(), $values);

    		return $data;
    	} catch (\PDOException $e) {
    		throw new \PDOException($e);
    	} catch (MapperException $e) {
    		die("Error: " . $e->getMessage());
    	}    	
    }
        

    /**
     * Порахувати кількість записів в базі по переданій умові $where
     */
	public function countWhere(Where $where, $switch = 'countStatement') {
		$this->_switch = $switch;
		return $this->columnWhere($where);   		
	}  
	
	/**
	 * Get one column data
	 * 
	 * @param Where $where
	 * @throws \PDOException
	 * @return mixed
	 */
	protected function columnWhere(Where $where) {
		$this->setCondition($where);
		 
		$queryStatement = $this->queryStatement();
		$values = $where->getTemplate();

		//\Agere\ZEngine::dump($queryStatement);
		//\Agere\ZEngine::dump($values);
		 
		try {
			$keyPrefix = $this->targetClassCollection(); // @todo more perfect
			$fetcher = new CacheContext(new PdoStrategy(self::$db, $keyPrefix, 'fetchColumn'));
			$fetcher->setCache($this->getWatcher()->getCache());
			$fetcher->setCacheOptions($this->cacheOptions('collection'));

			$queryStatement = ($queryStatement instanceof \Closure) ? $queryStatement() : $queryStatement;
			$data = $fetcher->execute($queryStatement, $values);
			
			//\Agere\ZEngine::dump($data);

			return $data;
		} catch (\PDOException $e) {
			throw new \PDOException($e);
		}
	}
	
	protected function queryStatement() {
		return $this->{$this->_switch}();
	}	
	
    /**
     * Повертає назву класу колекції.
     * Береться остання частина поточної назви класу і заміняється 'Mapper' -> 'Collection'
     * For example:
     * 		News_Mapper_NewsMapper -> News_Mapper_NewsCollection
     */
    protected function targetClassCollection() {
    	$parts = explode('\\', get_class($this));
    	$mapper_name = array_pop($parts);
    	$collection_name = str_replace('Mapper', 'DeferredCollection', $mapper_name);
    	return implode('\\', $parts) . '\\' . $collection_name;
    }
	
	public function cacheOptions($target) {
		$_target = $target;
		return isset($this->cacheOptions[$_target]) ? $this->cacheOptions[$_target] : false; //@FIXME
	}    
    
    public function save(Domain $obj) {
		try {
			//\Agere\ZEngine::dump($values);
			//\Agere\Debug::dump($obj); die(__METHOD__);
			$values = $obj->toArray(Domain::TO_ARRAY_FIRST_DEPTH);
			$newOrExists = (int) self::$db->save($this->docTable, $values);
			$id = $obj->getId() ? : self::$db->lastInsertId();
			$obj->setId($id);

			// If object is updated to add it to cache
			if ($newOrExists === 2 || $newOrExists === 3) { //@FIXME unknown how return 3
				//\Agere\Debug::dump($this->targetClass()); die(__METHOD__);
				$watcher = $this->getWatcher();
				$watcher->delete($this->targetClass(), $id); // @todo must to be more perfect
				$watcher->set(self::find($id)); // @todo thinking that is true?
			}
		} catch (\PDOException $e) {
			throw new \PDOException($e);
		}
    	$this->doSave($obj);
    }


    /*protected function _save(Domain $obj, $values) {
		try {
			//\Agere\ZEngine::dump($values);
			//\Agere\Debug::dump($obj); die(__METHOD__);
			
			$newOrExists = (int) self::$db->save($this->docTable, $values);
			$id = $obj->getId() ? : self::$db->lastInsertId();
	 		$obj->setId($id);
	 		 		// If object is updated to add it to cache
	 		if ($newOrExists === 2 || $newOrExists === 3) { //@FIXME unknown how return 3
	 			//\Agere\ZEngine::dump($this->getWatcher()); die(__METHOD__);
	 			$watcher = $this->getWatcher();
	 			$watcher->delete($this->targetClass(), $id); // @todo must to be more perfect
	 			$watcher->set(self::find($id)); // @todo thinking that is true?
	 		}
	 		
		} catch (\PDOException $e) {
			throw new \PDOException($e); 
		}
    }*/

	public function saveCollection(AbstractCollection $collection) {
		$arrayValues = [];
		foreach ($collection as $obj) {
			if (get_class($obj) === $this->targetClass()) { //@FIXME
				$arrayValues[] = $obj->toArray(Domain::TO_ARRAY_FIRST_DEPTH);
			}
		}

        //\Agere\Debug::dump(get_class($obj), $this->targetClass(), $arrayValues); die(__METHOD__);
		$result = self::$db->multipleSave($this->docTable, $arrayValues);
		$this->doSaveCollection($collection);

		return $result;
	}

    public function delete($id) {
		self::$db->exec("DELETE FROM `{$this->docTable}` WHERE `id` = '{$id}'");
    	$this->doDelete($id);
    	$this->getWatcher()->delete($this->targetClass(), $id);
    }

	/**
	 * Додати об'єкт в кеш.
	 * Можна передавати лише Domain
	 */
	/*protected function addToMap(Domain $obj) {
		$watcher = $this->getWatcher();
		return $watcher::add($obj, $this->cacheOptions());
	}*/
	
	/**
	 * @todo
	 * @deprecated DELETE THIS
	 */	
	public function getTargetClass() {
		return $this->targetClass();
	}
	
	/**
	 * @access protected
	 * @return \Agere\Domain\Mapper\DomainObjectFactory
	 */
	public function getObjectFactory() {
		if(!$this->dofact) {
			$this->dofact = \Agere\Domain\Factory\Helper::getObjectFacroty($this->targetClass());
			$this->dofact->setWatcher($this->getWatcher());
		}
		return $this->dofact;
	}
	
	/**
	 * @todo
	 * @access protected
	 */
	public function getWatcher() {
		return \Agere\Domain\Domain::getWatcher();
	}

	/**
	 * Get source name for entity
	 * 
	 * This can be db, xml, web-service name...
	 * 
	 * @return string
	 */
	public function getSourceName() {
		return $this->docTable;
	}
	
	
	
	/*****
	 * Методи котрі йдуть нижче прямого відношення до шаблону Data Mapper не мають.
	 * Дані методи полегшують роботу, звільняючи від написання не потрібного коду.
	 *****/
	/**
	 * Встановлюэм обєкт з котрого буде формуватись WHERE.
	 * @param Where $condition
	 */
	protected function setCondition(Where $condition) {
		$this->condition = $condition;
	}
	
	/**
	 * Повертає умови для WHERE.
	 * Переадресується на Query_Query::buildWhere().
	 * Параметр відповідає назві аліаса таблиці.
	 */
	protected function getCondition() {
		$condition = $this->condition->buildWhere($this->alias);
		return $condition;
	}
	
	public function limit($count) {
		if(is_numeric($count) && $count > 0) {
			$this->limit = $count;
		} else {
			$this->limit = false;
		}
		return $this;
	}
	
	public function offset($start) {
		if(is_numeric($start) AND $start > 0) {
			$this->offset = $start;
		}
		return $this;
	}
	
	public function clear() {
		$this->orderBy = array();
		$this->groupBy = array();
		
		return $this;
	}
	
	protected function getAlias($alias = null) {
		$_alias = !is_null($alias) ? $alias : $this->alias;
		
		return $_alias ? "`{$_alias}`." : '';
	}
	
	/**
	 * Parse value if it isn't simple type 'field'
	 * 
	 * @param string $field
	 * @return array $carcass First element equals name field. If $field is specific second element will be template
	 * 				 $carcass(0 => "date_publication",
	 * 						  1 => "DATE_FORMAT(?, '%d-%m-%Y')")		  
	 */
	private function _parseField($field) {
		//ZEngine::dump($field);
		
		if( preg_match('/^([a-zA-Z_]+)$/', $field, $matches) ) { // date_publication
			$carcass[] = $matches[1];
			$carcass[] = null;
			
		} elseif( preg_match('/[a-zA-Z_]+\(([a-zA-Z_]+)\s*,\s*.*\)/u', $field, $matches) ) { // DATE_FORMAT(date_publication, '%d-%m-%Y') //@FIXME
			$carcass[] = $matches[1];
			$carcass[] = str_replace($matches[1], '?', $field);
			
		} else { // nothing
			$carcass[] = '';
			$carcass[] = null;
		}
		
		return $carcass;
	}	
	
	/**
	 * Встановлюєм поле по котрому проводити сортування
	 * For example:
	 * 		$mapper->limit(25)->offset(10)->orderBy('name')->orderBy('date_add','DESC')->find(58965);
	 * 		$mapper->orderBy('DATE_FORMAT(date_publication, '%d-%m-%Y')', 'DESC')
	 * 		$mapper->orderBy('priority', 'DESC', 'dns')
	 * 
	 * @param string $order
	 * @param string $sort 	Порядок сортування. Допустимі значення ASC | DESC
	 * @param string $alias Will be use in ORDER BY as alias table. By default use param Model_News_Mapper_NewsMapper::$alias. 
	 * @return \Agere\Domain\Mapper\AbstractMapper
	 */	
	public function orderBy($field, $sort = 'ASC', $aliasAnother = null) {
		
		$alias = $this->getAlias($aliasAnother);
		$fieldParsed = $this->_parseField($field);
		
		//ZEngine::dump($alias);
		//ZEngine::dump($fieldParsed);
		
		$_field = "{$alias}`{$fieldParsed[0]}`";
		$_field = is_null($fieldParsed[1]) ? $_field : str_replace('?', $_field, $fieldParsed[1]); // @FIXME if recieve FALSE value insted of NULL
		
		$this->orderBy[] = "$_field {$sort}";
		return $this;
	}
	
	/**
     * Повертає стрічку по котрій виконувати сортування в MySQL.
     * Встановлене останнє значення перезаписує всі попередні.
     * @return string 
     */
	protected function getOrderBy() {
		$order = '';
		if($this->orderBy) {
	       $order = " ORDER BY " . implode(', ', $this->orderBy);
		}
		return $order;
	}	
	
	/**
	 * Встановлюєм поле по котрому проводити сортування
	 * For example:
	 * 		$mapper->limit(25)->offset(10)->orderBy('name')->orderBy('date_add','DESC')->find(58965);
	 * 
	 * @param string $order
	 * @param string $sort 	Порядок сортування. Допустимі значення ASC | DESC
	 */	
	public function groupBy($field, $aliasAnother = null) {
		$alias = $this->getAlias($aliasAnother);

		$_field = (strpos($field, '.') === false) ? "{$alias}`{$field}`" : $field;
		
		$this->groupBy[] = "$_field";
		
		return $this;
	}
	
	/**
     * Повертає стрічку по котрій виконувати сортування в MySQL.
     * Встановлене останнє значення перезаписує всі попередні.
     * @return string 
     */
	protected function getGroupBy() {
		$group = '';
		if(isset($this->groupBy) && $this->groupBy) {
	       $group = " GROUP BY " . implode(', ', $this->groupBy);
		}
		return $group;
	}		
	
	/**
	 * Формуємо LIMIT для MySQL.
	 * 
	 * Note: Якщо не потрібна умова LIMIT в запиті просто встановіть мінусове значення
	 * 	$mapper->limit(-1);
	 * 
	 * @return string $_limit
	 */	
	protected function getLimit() {
		if($this->limit !== false) {
			$limit = " LIMIT " . ($this->offset * $this->limit) . ", " . $this->limit;
			$this->_limit = $limit;
		}
		return $this->_limit;
	}
	
	/*protected function getLang($param) {
		$method = 'get' . ucfirst($param);
		return \Agere\Base\App\Registry::getLang()->$method();
	}*/
	
} 