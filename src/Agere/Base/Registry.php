<?php 
namespace Agere\Base;

abstract class Registry {
	
	private static $instance;
	
	private function __construct() {}
	
	static function getInstance() {
		 if (!isset(self::$instance)) {
		 	self::$instance = new self();
		 }
		 return self::$instance;
	}
	
	protected function get($key){
		if(isset($this->values[$key])){
			return $this->values[$key];
		}
		return null;
	}
	
	protected function set($key, $obj){
		$this->values[$key] = $obj;
	}
		
}