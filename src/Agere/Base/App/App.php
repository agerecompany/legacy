<?php
namespace Agere\Base\App;

use Agere\Base\App\Helper,
	Agere\Base\Registry,
	Agere\Controller\Request\RequestFactory,
	Agere\Controller\Request;
	

class App {
	
	private function __construct() {}
	
	static public function getInstance() {
		if(!isset(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}	
	
	/**
	 * Start execute application. 
	 * 
	 * Set global config, etc.
	 *
	 * @todo this 
	 */
	public static function init() {
		self::handleRequest();
		
		$applicationHelper = Helper::getInstance();
		$applicationHelper->init();
	}
	
	
	/**
	 * Start application
	 */
	public static function handleRequest() {
		$request = new Request(RequestFactory::create());
		
		RequestRegistry::setRequest($request);

		$router = Router::getInstance();
		$router->run($request);

	}

}