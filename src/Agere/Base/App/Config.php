<?php
namespace Agere\Base\App;

use Agere\Util\ArrayStaff;

class Config {
	
	/**
	 * Cashe config param
	 * @var array
	 */
	protected $cash = array();
	
	public function __construct($configArray) {
		$this->config['global'] = ArrayStaff::arrayToObject($configArray);
	}
	
	public function toArray() {
		return $this->config;
	}
	
	/**
	 * Get category from section
	 * Attribute can be a simple string 'database', return data from database.
	 * Also can be a expand string 'database.param', return data from database.param and etc.
	 * 
	 * @param string $param @todo refactoring to $config->get('moduleName')->param->value
	 * 
	 */
	public function get($key, $param = null) {
		$hash = md5($key);
		$exists = $this->getFromMap($hash);
		if($exists) {
			return $exists;
		}

		$result = isset($this->config[$key]) ? $this->config[$key] : null;
		
		$this->addToMap($hash, $result);
		
		return $result;
	}
	
	public function set($key, $values) {
		$this->congif[$key] = ArrayStaff::arrayToObject($values);
	}
	
	/**
	 * Check exists config param
	 * @param string $section  	Section in config file.
	 * @param string $path		Array formatting string ['Agere']['version]
	 */
	public function exists($section, $path) {
		$result = '';
		eval('$result = isset($this->config["'. $section .'"]' . $path . ');'); // $this->config['database']['param'] @FIXME delete eval
		return $result;
	}
	
	/**
	 * Cashing config param
	 */
	protected function getFromMap($hash) {
		if(isset($this->cashe[$hash])) {
			return $this->cashe[$hash];
		}
		return false;
	}
	
	protected function addToMap($hash, $param) {
		$this->cash[$hash] = $param;
	}
}
