<?php
namespace Agere\Domain\Mapper;

use \Agere\Db\Query\Where;

interface IMapperStrategy {
	
	/**
	 * Find object by id.
	 * 
	 * @param integer $id
	 * @return \Agere\Domain\Domain $object
	 * @throws \PDOException
	 */
	public function find($id);

	/**
	 * Find by condition.
	 * 
	 * Note: Return value depend from concrete strategy.
	 * 
	 * @param Where $where
	 * @throws \PDOException
	 */
	public function findWhere(Where $where);

	/**
	 * Your personal find by...
	 * 
	 * Attantion: If you want use personal select you must create logic in this method.
	 * Use this methos at your own risk.
	 * 
	 * @param mixed $data
	 * @throws \PDOException
	 */
	public function findBy($data);
	
    /**
     * Рахувати кількість новин в базі по переданій умові $whereю
     * 
     * @param Where $where
     * @return integer $count
     */
	public function countWhere(Where $where);
    
	/**
	 * Save object.
	 * 
	 * @param \Agere\Domain\Domain $obj
	 * @return boolean	@todo
	 */
    public function save(\Agere\Domain\Domain $obj);
    
   	/**
   	 * Delete object by id.
   	 * 
   	 * @param integer $id
   	 * @return boolean @todo
   	 */
	public function delete($id);

	/**
	 * Create object by given data.
	 * 
	 * @param array $array
	 * @return \Agere\Domain\Domain $obj | false
	 */
	//public function createObject($array);
	
	/**
	 * Limitation selected values.
	 * 
	 * @param integer $count
	 * @return self
	 */
	public function limit($count);

	/**
	 * Offset selected values.
	 * 
	 * @param integer $start
	 * @return self
	 */	
	public function offset($start);
	
	/**
	 * Reset by default.
	 */
	public function clear();
	
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
	 */	
	public function orderBy($field, $sort = 'ASC', $aliasAnother = null);
	
	/**
	 * Встановлюєм поле по котрому проводити сортування
	 * For example:
	 * 		$mapper->limit(25)->offset(10)->orderBy('name')->orderBy('date_add','DESC')->find(58965);
	 * 
	 * @param string $order
	 * @param string $sort 	Порядок сортування. Допустимі значення ASC | DESC
	 */	
	public function groupBy($field);

	/**
	 * Define method current select.
	 * 
	 * @return Class name selected object
	 */
	//public function target();
	
	/**
	 * Get cache option for collection.
	 * 
	 * @return array
	 * @link http://www.php.net/manual/en/memcache.add.php
	 */
	public function cacheOptions($key);
	
}