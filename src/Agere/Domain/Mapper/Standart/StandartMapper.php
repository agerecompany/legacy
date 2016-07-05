<?php
namespace Agere\Domain\Mapper\Cache;

use Agere\Domain\Mapper\AbstractMapper;

abstract class StandartMapper extends AbstractMapper {
	
	/**
	 * @todo Look in the book
	 * @see Agere\Domain\Mapper.AbstractMapper::findWhere()
	 */
	protected function _findWhere($findStatement, $template) {
    	$findBy = self::$db->prepare( $findStatement );
		$findBy->execute( $template );
 		$objects = $findBy->fetchAll(\PDO::FETCH_CLASS, $this->targetClass());
 		$findBy->closeCursor();
 		
 		$collectionName = $this->target();
		$collection = new $collectionName($objects);
		return $collection;  
    }
}