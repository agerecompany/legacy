<?php
namespace Agere\Domain\Text;

use Agere\Domain\Mapper\Collection\ICollectionStrategy;
use Motor\Lang\Model\Lang\Lang;

class Texts {
	
	/**
	 * Text collection
	 * 
	 * @var ICollectionStrategy
	 */
	private $_textCollection;
	
	/**
	 * Current Domain text object
	 * 
	 * Object appropriate current system lang by default.
	 * 
	 * @var \Agere\Domain\Domain
	 */
	private $_text;
	
	/**
	 * Current Domain Lang object
	 * 
	 * @var Lang
	 */
	private $_lang;
	
	/**
	 * Lazy load for text object
	 * 
	 * @var bool
	 */
	private $_run = false;
	
	public function __construct(ICollectionStrategy $collection, Lang $lang) {
		$this->_textCollection = $collection;
		$this->_lang = $lang;
		
		$this->_text = $this->getFakeObject(); // create fake Text object		
		
		//$this->changeLang(\Agere\Base\App\Registry::getLang()->getId());
	}
	
	/**
	 * Get active Domain text object
	 * 
	 * @return \Agere\Domain\Domain
	 */
	public function getText() {
		if (!$this->_run) {
			$this->changeLang($this->_lang->getId());
			$this->_run = true;
		}
		return $this->_text;
	}
	
	/**
	 * Get collection all Domain object
	 * 
	 * @return \Agere\Domain\Mapper\Collection\ICollectionStrategy
	 */
	public function getCollection() {
		return $this->_textCollection;
	}
	
	/**
	 * Change default Domain text object by lang id
	 * 
	 * @param int $langId
	 */
	public function changeLang($langId) {
		foreach ($this->_textCollection as $text) {
			if ($text->getLangId() == $langId) {
				$this->_text = $text;
				return $this->_text;
			}
		}
		//\Agere\ZEngine::dump($langId, $this->getFakeObject());
		return $this->getFakeObject();
	}
	
	public function getFakeObject() {
		$className = $this->_textCollection->targetClass();
		$classNameDto = $className . 'Dto';
		return new $className(new $classNameDto);
	}
	
	public function __call($method, $params) {
		return $this->_text->{$method}();
	}
	
}