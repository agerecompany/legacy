<?php
namespace Agere\Domain\Mapper\Cache\Strategy;

use Agere\Domain\Mapper\Cache\AbstractCache;

class PdoStrategy extends AbstractCache { 
	
	protected function fetch($findStatement, array $values) {
		$method = $this->data ? : 'fetchAll';
		return $this->$method($findStatement, $values);
	}

	private function fetchAll($findStatement, array $values) {
		$findBy = $this->sourceClient->prepare($findStatement);
		$findBy->execute($values);
		$arrayData = $findBy->fetchAll(\PDO::FETCH_ASSOC);
		$findBy->closeCursor();
		
		return $arrayData;		
	}
	
	protected function fetchColumn($findStatement, array $values) {
		$findBy = $this->sourceClient->prepare($findStatement);
		$findBy->execute($values);
		$arrayData = $findBy->fetchColumn();
		$findBy->closeCursor();
	
		return $arrayData;
	}
	
}