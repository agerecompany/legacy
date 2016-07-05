<?php
namespace Agere\Log\Logger;

use Agere\Log;

class Db extends Log\Logger {
	
	protected $_logFile = "var/log/error.log";
	
	public function error(\Exception $e, array $data = null) {
		//die('tro');
		$this->createFormat();
		$this->setEventItem('error', $e->getTraceAsString());
		$this->setEventItem('query', $data['query']);
		
		parent::error($e);
	}
	
	protected function createFormat() {
		$format = $this->_format . PHP_EOL
			  	 .'Query: %query%' . PHP_EOL
			  	 .'Error: %error%' . PHP_EOL;
		$this->_format = $format;
	}
}
 