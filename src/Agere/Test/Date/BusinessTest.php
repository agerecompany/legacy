<?php
/**
 * Class for calculate business time logic
 *
 * @category Agere
 * @package Agere_Date
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 08.07.13 20:40
 */

namespace Agere\Test\Date;

use Agere\Date\Business;

class BusinessTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \Agere\Date\Business
	 */
	protected $business;

	protected function setUp() {
		$this->business = new Business();
	}

	public function testConvertToTimestamp() {
		$this->assertEquals(1373403600, $this->business->convertToTimestamp(1373403600));
		$this->assertEquals(strtotime('today'), $this->business->convertToTimestamp('today'));
		$this->assertEquals(strtotime('2012-04-12 15:36:02'), $this->business->convertToTimestamp('2012-04-12 15:36:02'));
	}


	/*public function testTimeOfWeek($time) {
		$time = $this->convertToTimestamp($time) ;
		$secondsInWeek = (7 * 24 * 3600);
		return (($time - strtotime('monday 00:00')) % $secondsInWeek + $secondsInWeek) % $secondsInWeek;
	}*/

	public function testIsBusiness() {
		$business = $this->business;

		$this->assertFalse($business->isBusiness('monday 08:59'), 'Monday 08:59 is business time');
		$this->assertTrue($business->isBusiness('monday 09:00'), 'Monday 09:00 is not business time');
		$this->assertTrue($business->isBusiness('wednesday 14:00'), 'Wednesday 14:00 is not business time');
		$this->assertTrue($business->isBusiness('friday 17:00'), 'Friday 17:00 is not business time');
		$this->assertTrue($business->isBusiness('friday 18:00'), 'Friday 18:00 is not business time');
		$this->assertFalse($business->isBusiness('friday 18:01'), 'Tuesday 18:01 is in business time');
		$this->assertFalse($business->isBusiness('tuesday 20:00'), 'Tuesday 20:00 is in business time');
		$this->assertFalse($business->isBusiness('saturday 12:00'), 'Saturday 12:00 is in business time');
		$this->assertFalse($business->isBusiness('sunday 10:00'), 'Sunday 10:00 is in business time');
	}

	public function testGetBusinessTime() {
		$business = $this->business;

		$this->assertEquals(false, $business->getBusinessTime('monday 08:50'));
		$this->assertEquals(false, $business->getBusinessTime('sunday'));
		$this->assertEquals(false, $business->getBusinessTime('friday 18:01'));
		$this->assertEquals(false, $business->getBusinessTime('2013-07-12 18:01'));
		$this->assertEquals(array('wednesday 09:00', 'wednesday 18:00'), $business->getBusinessTime('2013-07-10 13:50'));
		$this->assertEquals(array('monday 09:00', 'monday 18:00'), $business->getBusinessTime('monday 09:01'));
		$this->assertEquals(array('monday 09:00', 'monday 18:00'), $business->getBusinessTime('monday 12:00'));
		$this->assertEquals(array('friday 09:00', 'friday 18:00'), $business->getBusinessTime('friday 17:00'));
	}

	public function testGetBusinessPeriod() {
		$business = $this->business;

		$this->assertEquals(array('monday 09:00', 'monday 18:00'), $business->getBusinessPeriod('monday 09:15'));
		$this->assertEquals(array('wednesday 09:00', 'wednesday 18:00'), $business->getBusinessPeriod('tuesday 23:59'));
		$this->assertEquals(array('wednesday 09:00', 'wednesday 18:00'), $business->getBusinessPeriod('wednesday 00:10'));
		$this->assertEquals(array('friday 09:00', 'friday 18:00'), $business->getBusinessPeriod('friday 14:22'));
		$this->assertEquals(array('monday 09:00', 'monday 18:00'), $business->getBusinessPeriod('friday 20:45'));
		$this->assertEquals(array('monday 09:00', 'monday 18:00'), $business->getBusinessPeriod('saturday 11:30'));

		//die(__METHOD__);
	}

	function testCheckInRange() {
		$business = $this->business;

		$this->assertTrue($business->checkInRange('2009-06-17', '2009-09-05', '2009-08-28'));
		$this->assertTrue($business->checkInRange('2000-01-11', '2029-05-01', '2016-08-05'));
		$this->assertFalse($business->checkInRange('2013-07-11', '2013-05-01', '2013-06-18'));
	}


	/**
	 * Carry out this test very carefully. This can give error in any time of week.
	 * This test is relative to second week of july (friday 2013-07-12)
	 */
	public function testPlusBusinessHours() {
		$business = $this->business;

		$this->assertEquals((new \DateTime('friday 18:00'))->format('Y-m-d H:i'), $business->plusBusinessHours(9, new \DateTime('friday 09:00'))->format('Y-m-d H:i'));
		$this->assertEquals((new \DateTime('friday 12:00'))->format('Y-m-d H:i'), $business->plusBusinessHours(3, new \DateTime('friday 09:00'))->format('Y-m-d H:i'));
		$this->assertEquals((new \DateTime('thursday 18:00'))->format('Y-m-d H:i'), $business->plusBusinessHours(44, new \DateTime('friday 10:00'))->format('Y-m-d H:i'));
	}

}