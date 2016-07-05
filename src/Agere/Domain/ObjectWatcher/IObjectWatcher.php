<?php
/**
 * Identity map Pattern
 * 
 * @author Popov Sergiy
 */

namespace Agere\Domain\ObjectWatcher;

use Agere\Domain;

interface IObjectWatcher{

	/**
	 * Get Singelton ObjectWathcer
	 */
	//static function getInstance();
	
	/**
	 * Set Domain object to ObjectWatcher
	 * 
	 * If object exist it will be replaced new object
	 * 
	 * @param Domain $obj
	 */
	static function set(Domain\DomainInterface $obj);
	
	/**
	 * Add Domain object to ObjectWatcher
	 * 
	 * Checking if object exist nothing do otherwise add new object
	 * 
	 * @param Domain $obj
	 */
	static function add(Domain\DomainInterface $obj);
	
	/**
	 * Check exists Domain object
	 * 
	 * @param string $classname
	 * @param int $id
	 */
	static function exists($classname, $id);
	
	/**
	 * Do Domain object dirty
	 * @param Domain $obj
	 */
	static function addDirty(Domain\DomainInterface $obj);

	/**
	 * Add new Domain object to ObjectWatcher.
	 * New object isn't still save in db.
	 * 
	 * @param Domain $obj
	 */
	static function addNew(Domain\DomainInterface $obj);

	/**
	 * Do Domain object clean.
	 * This give opportunity don't save object to db.
	 * 
	 * @param Domain $obj
	 */
	static function addClean(Domain\DomainInterface $obj);

	/**
	 * Global object key
	 *
	 * @param Agere\Domain\Domain $obj
	 */
	public function globalKey(Domain\DomainInterface $obj);
		
	/**
	 * Perform object for something (save, update...)
	 */
	public function performOperation();
	
}