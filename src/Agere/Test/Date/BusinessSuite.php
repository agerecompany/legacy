<?php
namespace Agere\Test\Date;

class BusinessSuite extends \PHPUnit_Framework_TestSuite {
	
	public static function suite() {
		$suite = new BusinessSuite('BusinessTest');
		$suite->addTestSuite('\Agere\Test\Date\BusinessTest');
		return $suite;
	} 
	
}