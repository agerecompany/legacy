<?php
namespace Agere\Pagination;

class Pagination {
	
	/**
	 * Загальна кількість зписів
	 * @var int
	 */
	protected $_total;
	
	/**
	 * Кількість записів для показу на сторінці
	 * @var int
	 */
	protected $_onPage = 30;
	
	/**
	 * Кількість сторінок
	 * @var int
	 */
	protected $_countPages = 1;
	
	/**
	 * Поточна сторінка
	 * @var int
	 */
	protected $_page = 1;

	protected $strategy = null;
	
	public function __construct($on_page, $total = 0) {
		$this->_total = $total;
		$this->_onPage = $on_page;
		//$this->_countPages = $this->amountPage();
	}
	
	private function init() {
		$this->amountPage();
	}
	
	protected function amountPage() {
		$countPages = 0;
		if ( $this->_total > $this->_onPage ) { 
			$countPages = ceil($this->_total / $this->_onPage); 
		}
		$this->_countPages = $countPages;
		return $countPages; // FIXME перевірити чи не вплинуло це на інші плагіни, котрі реалізовані по даному принципу
	}
						 
	public function getCountPages() {
		return $this->_countPages;
	}
	
	public function getCurrentPage() {
		return $this->_page;
	}
	
	public function getTotal() {
		return $this->_total;
	}	

	public function setTotal($total) {
		$this->_total = $total;
		return $this;
	}

	/**
	 * Повертає поточну сторінку, або 0, якщо вона не встановлена.
	 * @return	string	Шаблон по якому вибираєм поточну сторінку з Url
	 */
	public function setCurrentPage($page) {
		$this->_page = (int)$page;
	}

	public function getOnPage() {
		return $this->_onPage;
	}

	/*public function setStrategy($strategy = null) {
		$this->strategy = $strategy;
	}*/

	public function getStrategy() {
		$paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\NullFill($this->getTotal()));
		$paginator->setCurrentPageNumber($this->getCurrentPage());
		$paginator->setItemCountPerPage($this->getOnPage());
		return $paginator;
	}

}