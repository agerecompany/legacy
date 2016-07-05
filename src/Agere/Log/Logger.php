<?php
namespace Agere\Log;

use Zend\Log as ZendLog;

abstract class Logger {
	/**
	 * Format string for log file
	 * 
	 * @ling http://framework.zend.com/manual/en/zend.log.formatters.html
	 * @var string
	 */
	protected $_format = "%timestamp% %priorityName% (%priority%):\n%message%";
	
	/**
	 * Path to log file
	 * @var string
	 */
	protected $_logFile = '';
	
	/**
	 * A item to every future event.
	 * 
	 * @see Zend\Log\Logger\setEventItem();
	 * @var array
	 */
	protected $_eventItem = array();
	
	public function log(array $data) {}
	
	public function error(\Exception $e, array $data = null) {
		$format = '/--' . PHP_EOL . $this->getFormat()  .'--/' . PHP_EOL;
				  
		$formatter = new ZendLog\Formatter\Simple($format);
		$writer = new ZendLog\Writer\Stream(DR . DS. $this->_logFile, 'a+');
		$writer->setFormatter($formatter);
		$logger = new ZendLog\Logger(); 
		$logger->addWriter($writer);
		foreach($this->getEventItem() as $name => $value) {
			$logger->setEventItem($name, $value);
		}	
		$logger->log(ZendLog\Logger::ERR, $e->getMessage());
	}

	public function getFormat() {
		return $this->_format;
	}
	
	public function getEventItem() {
		return $this->_eventItem;
	}
	
	public function setEventItem($name, $value) {
		$this->_eventItem[$name] = $value;
	}
}