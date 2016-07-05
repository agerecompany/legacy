<?php
namespace Agere\Domain\Mapper\Traits;

trait TargetTrait {
	
	/**
	 * Get model namespace
	 * 
	 * Call under Mapper namespace in Module
	 * 
	 * @return string
	 */
	public function targetClass() {
		$exploded = explode('\\', get_class($this));
		$partOfModelNamespace = array_slice($exploded, 0, count($exploded) - 2);
		$modelName = end($partOfModelNamespace);
		$target = implode('\\', $partOfModelNamespace) . '\\' . $modelName;
		
		return $target;
	}
		
}