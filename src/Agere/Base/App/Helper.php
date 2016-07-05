<?php
namespace Agere\Base\App;

use Agere\Console\Console;
use Locale;
use Agere\Base\App\Registry,
	Agere\Base\App\Config,
	Agere\Base\App\Exception,
	Agere\Base\Request\Registry as RequestRegistry;

/**
 * Клас являється сінглтоном, і не повертає ніяких даних.
 * Він являється помічником класу X_Base_Application_Registry.
 * Його робота заключається в розпарсені конфігураційного файлу за допомогою simplexml_load_file(),
 * і встановлені відповідних параметрів за допомогою статичних методів в класі X_Base_Application_Registry.
 * Цим самим звільнено клас X_Base_Application_Registry від роботи яку він не повинен виконувати.
 * На стр. 255-257 написано про рефакторинг коду, але я на даний момент (24.07.2011) не осилив як це зробити.
 * Точніше зробив але не зовсім як там написано. Тому продовжую розробку з надією на майбутнє просвітлення.
 * Ще повинно відбуватись кешування отриманих параметрів стр. 250-251. Після спроби вияснити як порівнювати
 * mime-поле модифікації файлу, нічого крім як запису в базу даних теперішньої дати модифікації і надалі 
 * порівнювати з останньою датою модифікації в голову не прийшло. Також оставив для майбутнього просвітлення. 
 * 
 * Головне що потрібно відмітити, - метод getOption() викликається тільки в випадку, 
 * якщо параментри конфігурації не були збережені в кеш-памяті обєкта Application_Registry
 * 
 * @author xaker
 */
class Helper {
	
	private static $instance;
	
	/**
	 * Path to config file
	 * 
	 * @var string
	 */
	private $options = [
		'global' => "/config/global.config.php",
		'application' => "/config/application.config.php",
	];
	
	/**
	 * Path to directory for save cache options
	 * 
	 * Notice: should set permition for writing
	 * 
	 * @var string
	 */
	private $freezedir = "/data/cache/options"; //@todo
	
	private $mtimes = array();
	
	private function __construct() {}
	
	static function getInstance() {
		if(!isset(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function init() {
		$this->_initBefore();
		$this->getOptions();
		$this->_initAfter();
	}
	
	public function setLang($lang) {
		$mapper = \Agere\Domain\Factory\Helper::getFinder('lang/lang');
		$mapper->setDb(Registry::getDb()); // Magic. Set db for all mapper object
		$langObj = $mapper->findByMnemo($lang);

		Registry::setLang($langObj);
	}
	
	private function getOptions() {
		//$reader = new \Zend\Config\Reader\Ini();
		//$data = $reader->fromFile(DR . $this->options);
		
		$global = require($this->options['global']);
		$application = require($this->options['application']);
		
		$this->process($global + $application);
	}
	
	private function process($data) {
		$siteStatus = $this->getSiteStatus();

		$config = new Config($data);
		
		if(strlen($siteStatus) > 0) {
			// create load modules file config
			/*foreach($modules as $module) {
				$config->set(strtolower($modlueName), $arrayModuleConfig);
			}*/
		}
		Registry::setConfig($config);
	}

	/**
	 * Get curren site status.
	 * 
	 * If current url contain one of these value
	 * then select some direction in config file.
	 * 
	 * dev - development This part site domain accord enviroment on developper workstation 
	 * mirror - staging Pretesting release
	 * mirror2 - testing Some new features testing on this site
	 * 
	 * @return string Curren site status
	 */
	private function getSiteStatus() {
		switch($this->getMachineStatus()) {
			case 'loc':
			case 'dev': $status = 'development';
				break;
			case 'mirror': $status = 'staging';
				break;
			case 'mirror1': $status = 'testing';
				break;
			default: $status = ''; //prodaction
		}
		return $status;
	}
	
	/**
	 * For develope "dev" -> unn.com.ua.dev
	 */
	private function getMachineStatus() {
		if(preg_match("/(dev|mirror|loc)/", (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] :  ''), $matches) ) { //@FIXME
			defined('DEV')
			||define('DEV', $matches[1]);
		} else {
			defined('DEV')
			|| define('DEV', false);
		}		
		return DEV;
	}
	
	private function ensure ($expr, $message) {
		if (!$expr) {
			throw new Exception($message);
		}
	}
	
	/**
	 * To do something before general execute
	 */
	protected function _initBefore() {
	}
	
	/**
	 * To do something after general execute
	 */
	protected function _initAfter() {
		$this->_initMemcache();
		$this->_initDb();
		$this->_initLang();
		$this->_initLocale();
	}
	
	protected function _initDb() {
		$database = Registry::getConfig()->get('global')->database;
		//$dsn = "mysql:dbname={$ZONT['dbname']};host={$ZONT['zonthost']};port={$ZONT['port']}";
		Registry::setDSN($database->dsn);
		
		$db = \Agere\Db::ConnectDB($database->database, $database->hostname, $database->username, $database->password, $database->port);
		Registry::setDb($db);
	}
	
	/**
	 * Handle current language
	 */
	private function _initLang() {
		$request = RequestRegistry::getRequest();
		
		$langDef = Registry::getConfig()->get('global')->language->lang;
		$langStr = $request->getParam('lang', $langDef);

		$this->setLang($langStr);
	}	
	
	private function _initLocale() {
		$lang = Registry::getLang();
		
		$localeValue = $lang->getLocale();
		
		Locale::setDefault($localeValue);
		
		//$tranlationPath = DR . DS . "locale";
		
		// @todo recursivly add module translates to translator
		//$translator = new \Zend\I18n\Translator\Translator();
		//$translator->addTranslationFile('gettext', $tranlationPath . DS . Locale::getDefault() . "/LC_MESSAGES/default.mo", 'default', Locale::getDefault());
		//Registry::setTranslate($translator);
	}
	
	/**
	 * Initialization memcache
	 */
	private function _initMemcache() {
		$cachePrefix = Registry::getConfig()->get('global')->cache->prefix;
		\Agere\MemcacheAirweb::setPrefix($cachePrefix);	
	}	

} 