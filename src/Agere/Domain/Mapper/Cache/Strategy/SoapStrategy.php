<?php
namespace Agere\Domain\Mapper\Cache;

class SoapStrategy extends AbstractCache { 
	
	protected function fetch($findStatement, array $values) {
		$arrayData = $this->sourceClient->Invoke( $findStatement, $values );
		return $arrayData;
	}
	
	protected function cacheKey($findStatement, $values) {
		unset($values['SessionGUID']);
		unset($values['PersonCodeU']);
		unset($values['ActualDate']);
		
		$key = $this->keyPrefix . '_' . md5( serialize( $findStatement ) . serialize( $values ) );
		return $key;
	}

}