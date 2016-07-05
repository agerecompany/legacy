<?php
namespace Agere\Domain;

/**
 * Setter and getter for object.
 * 
 * Reduce grueling that methods.
 * 
 * @author Serzh
 */
trait SetGetTrait {

	/**
	 * @return \Agere\Domain\Domain | \Agere\Domain\Dto\Dto
	 */
	/*private function getObject() {
		$that = $this;
		if (property_exists($this, 'dto')) {
			$that = $this->dto;
		}
		return $that;
	}*/
	
	/**
	 * Method for set properties through setters
	 * 
	 * Prepare under_line notification to upperCase notification
	 * 
	 * @param array $properties
	 */
    public function setProperties(array $properties) {
    	//$that = $this->getObject();
        foreach ($properties as $property => $value) {
        	$property = $this->preparePropertyName($property);
        	$method = 'set'. ucfirst($property);
            $this->{$method}($value);
        }
        return $this;
    }

    /**
     * This method works by principle C# setters and getters. 
     * For example: public $price { get; set; }
     * 
	 * @see http://blog.byndyu.ru/2011/12/domain-driven-design.html
	 * @param str $name
	 * @param mixed $arguments
	 */
	public function __call($name, $arguments = null){
		if(preg_match('/^(get|set)([a-zA-Z0-9]+)$/', $name, $matches)) {
			$propName = $matches[2];
			$property = strtolower($propName{0}) . substr($propName, 1);
		
			if(!property_exists($this, $property)) {
				$className = get_class($this);
				throw new DomainException("You call unregistered method {$className}::{$name}(). This class doesn't have declared property {$className}::\${$property}. Please check interface and declared properties your class {$className}.");
			}
			
			switch($matches[1]) {
				case 'get': 
					return $this->{$property};
				case 'set' :
					$this->{$property} = $arguments[0]; //@FIXME which value have $arguments
			}
			
		} else {
			$className = get_class($this);
			throw new DomainException("You can call unregistered methods {$className}::{$name} anounce only as setters or getters. Calling method must start itself name from get or set.");
		}
	}	
	
	/**
	 * @param string|int $name
	 * @param mixed $value
	 */
    public function __set($name, $value) {
        $property = $this->preparePropertyName($name);
        $this->{$property} = $value;
    }
    
    /**
     * @param string $name
     */
    public function __get($name) {
    	$method = 'get' . ucfirst($name);
        return $this->{$method}();
    }

    /**
     * Parse name db field to Domain standard.
	 * author_name -> authorName
	 *
     * @param string $name
	 * @return string $property
     */
    protected function preparePropertyName($name) {
        $name_part = explode('_', $name);
    	
    	$property = $name_part[0]; //@FIXME perfomance. Might will be array_shift better?
    	foreach($name_part as $key => $part) {
    		if($key !== 0) {
    			$property .= ucfirst($part);
    		}
    	}    
    	return $property;
    }

}