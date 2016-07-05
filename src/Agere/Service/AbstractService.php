<?php
namespace Agere\Service;

use Agere\Service\Factory\Helper as ServiceFactoryHelper,
	Agere\Domain\Factory\Helper as DomainFactoryHelper,
	Agere\Pagination\Pagination,
	Agere\Domain\Mapper\IMapperStrategy,
	Agere\Db\Query\Where,
	Agere\Service\ServiceException,
	Magere\Lang\Service\LangService;
	//Magere\Tree\Model\Section\Mapper\SectionMapper;
	

abstract class AbstractService implements LayerInterface {
	/**
	 * Об'єкт пейджера
	 * 
	 * @var //\Magere\Model\Pager\Stepper
	 */
	//protected  $_pager;
	
	/**
	 * Об'єкт сутності (новина, блог, стаття)
	 * 
	 * @var \Agere\Domain\Domain
	 */
	protected $_item;
	
	/**
	 * Колекція об'єктів сутностей (новини, блоги, статті)
	 * 
	 * @var \Agere\Domain\Mapper\Collection\AbstractCollection
	 */
	protected $_collection;
	
	/**
	 * Service for working with languages
	 *
	 * @var \Magere\Lang\Service\LangService
	 */
	protected static $langService;
	
	/**
	 * Tree Section mapper
	 * 
	 * @var //\Magere\Section\Model\Section\Mapper\SectionMapper
	 */
	//protected static $sectionMapper;
	
	/**
	 * Array of Mapper objects
	 * 
	 * @var array
	 */
	protected $mappers = array();
	
	/**
	 * Object constuction sql where conditions
	 *
	 * @var \Agere\Db\Query\Where
	 */
	protected $where;

	protected $cache = null;

    protected $parentNamespace = 'Magere';

	protected $paginator = null;
	
	/**
	 * Constuctor
	 * 
	 * Need set langService and sectionMapper only one time,
	 * in the time creation any first service.
	 * Next time will use previously set objects.
	 * 
	 * @param LangService $langService
	 * @param unknown_type $sectionMapper
	 */
	public function __construct(LangService $langService = null, SectionMapper $sectionMapper = null) {
		//\Agere\ZEngine::dump($langService); die(__METHOD__);
		//$this->langService = $langService ? : ServiceFactoryHelper::getFinder('lang');
		//$this->sectionMapper = $sectionMapper ? : DomainFactoryHelper::getFinder('section/section');
		//self::$langService = $langService ? : self::$langService ? : ServiceFactoryHelper::create('lang');
		//self::$sectionMapper = $sectionMapper ? : self::$langService ? : DomainFactoryHelper::getFinder('section/section');
		$this->where = new Where();
	}
		
	/**
	 * Повертає сутність (DomainObject) по переданому $id.
	 * 
	 * @param int $id
	 * @return \Agere\Domain\Domain $item
	 */
	public function getItem($id) {
	}
	
	/**
	 * Повертає колекцію сутностей
	 * 
	 * @param \Agere\Db\Query\Where $where
	 * @return \Agere\Domain\Mapper\Collection\AbstractCollection
	 */
	public function getItemCollection(\Agere\Db\Query\Where $where) {
	}
	
	public function getPager($currPage = 0) {
		if (!$this->paginator) {
			$this->paginator = new Pagination(36);
			$this->paginator->setCurrentPage($currPage);
		}

		return $this->paginator;
		//return ServiceFactoryHelper::getFinder('paginator')->setPaginator(, 36);
	}

	/**
	 * If given atribute and it is Where object then return its 
	 * otherwise return empty Where object
	 * 
	 * @param \Agere\Db\Query\Where $where
	 * @return \Agere\Db\Query\Where
	 */
	public function getWhere(\Agere\Db\Query\Where $where = null) {
		$where || $where = $this->where->clear(); //@FIXME
		return $where;
	}
	
	/**
	 * @return SectionMapper
	 */
	public function getSectionMapper() {
		return self::$sectionMapper;
	}
	
	/**
	 * @return SectionMapper
	 */
	public function getLangService() {
		return self::$langService;
	}
	
	public function setLangService($langService) {
		self::$langService = $langService;
	}

    public function getParentNamespace() {
        return $this->parentNamespace;
    }
	
	/**
	 * Set mapper by key
	 *
	 * The key must be mapper name without last part of name (Mapper).
	 * Mapper will take from current module namespace
	 *
	 * @param string
	 * @param IMapperStrategy $mapper
	 */
	public function setMapper($key, IMapperStrategy $mapper = null) {
		if ($mapper === null) {
             $mapper = DomainFactoryHelper::getFinder($key);
		}
	
		$this->mappers[$key] = $mapper;
		return $this;
	}
	
	/**
	 * Get mapper for a key
	 *
	 * Return specific Mapper for current module
	 *
	 * @param string $key
	 * @return \Agere\Domain\Mapper\AbstractMapper
	 */
	public function getMapper($key) {
		isset($this->mappers[$key]) || $this->setMapper($key);
		return $this->mappers[$key]->clear();
	}	

	/*public function setCache($cache) {
		$this->cache = $cache;
	}*/

	public function getCache() {
		//return $this->cache;
		return DomainFactoryHelper::getMaintenance('Agere\Memcache');
	}

	/**
	 * Get namespace of current module
	 * 
	 * @return string
	 */
	public function getModuleNamespace() {
		$class = get_class($this);
		$namespace = substr($class, 0, strpos($class, '\\Service'));
		return $namespace;
	}
	
}