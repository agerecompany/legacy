<?php
namespace Agere\Controller;

use Zend\View\Model\ViewModel;
use Agere\Domain\Mapper\AbstractMapper;

trait DeleteActionTrait {
	/**
	 * Current main mapper
	 *
	 * @var \Agere\Domain\Mapper\MapperContext
	 */
	protected $mapper;
	
	public function XAction() {
		$viewModel = $this->commandX();
		return $viewModel->setTemplate("magere/{$this->getControllerName()}/delete.phtml");
	}
	
	/**
	* Prepare delete item from db.
	* Id that should will be delete add to SessionContainer
	*/
	protected function commandX() {
		$request = $this->getRequest();
		$viewModel = new ViewModel();
		
		if ($request->isPost()) {
			$values = $request->getPost()->toArray();
			$sessionDelete = $request->getPost('delete');

			$mapper = $this->getMapper();
			$sessionCount = count($sessionDelete);
			for($i = 0; $i < $sessionCount; $i++){
				$sessionDelete[$i]['info'] = $mapper->find($sessionDelete[$i]['id']);
			}
	
			//$session = new \Zend\Session\Container('base');
			$this->session()->offsetSet('delete', $sessionDelete);
	
			$viewModel->setVariables(['sessionDelete' => $sessionDelete]);
		}
		return $viewModel;
	}	


	public function deleteAction() {
		$this->commandDelete();
		$route = $this->getEvent()->getRouteMatch();
		$this->redirect()->toRoute('default', ['controller' => $route->getParam('controller'), 'action' => 'index', 'lang' => $route->getParam('lang')]);
	}	
	
	/**
	 * Delete items
	 */
	 protected function commandDelete() {
		$request = $this->getRequest();
		
		if ($request->isPost()) {
			$values = $request->getPost()->toArray();
		
			//$session = new \Zend\Session\Container('base');
			$sessionDelete = $this->session('delete');
			
			$mapper = $this->getMapper();
			$sessionCount = count($sessionDelete);
			for($i = 0; $i < $sessionCount; $i++) {
				if (isset($values['delete'][$sessionDelete[$i]['id']]['id'])) {
					$mapper->delete($values['delete'][$sessionDelete[$i]['id']]['id']);
				}
			}
		}
	}
	
	/**
	* Set acting mapper for current action.
	*
	* @retrun $mapper
	*/
	protected function getMapper() {
		return $this->mapper;
	}
	
	/**
	* Set acting mapper for current action.
	*
	* @param \Agere\Domain\Mapper\AbstractMapper $mapper
	*/
	protected function setMapper(AbstractMapper $mapper) {
		$this->mapper = $mapper;
	}
	
	/**
	 * @todo find out how get origin controller name instead of alias in config.
	 * 		When is occurring render page get exactly this name, try to find it.
	 */
	protected function getControllerName() {
		$tmp = explode('\\', get_class($this));
		$class = array_pop($tmp);
		return strtolower(substr($class, 0, strpos($class, 'Controller')));
	}
}