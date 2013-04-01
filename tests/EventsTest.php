<?php
/**
 * @package    SugiPHP
 * @subpackage Events
 * @author     Plamen Popov <tzappa@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php (MIT License)
 */

use \SugiPHP\Events\Dispatcher;
use \SugiPHP\Events\Event;

class EventsTests extends PHPUnit_Framework_TestCase
{
	
	public $eventsDispatched;
	public $dispatcher;
	public $event;

	public function setUp()
	{
		if (version_compare(PHP_VERSION, "5.4.0") < 0) {
			$this->markTestSkipped("EventsTest requires PHP version >= 5.4");
		}

		$this->eventsDispatched = 0;
		$this->dispatcher = new Dispatcher();
		$this->event = new Event("unit.test");
	}

	public function testOneListener()
	{
		// creating listener
		$this->dispatcher->addListener("unit.test", function($e) {
			$this->eventsDispatched++;
		});
		// firing
		$this->dispatcher->dispatch($this->event);
		// checking it was fired
		$this->assertEquals(1, $this->eventsDispatched);
	}

	public function testOneListenerTriggerOtherEver()
	{
		// creating listener
		$this->dispatcher->addListener("unit.test", function($e) {
			$this->eventsDispatched++;
		});

		$this->dispatcher->dispatch(new Event("unit.test2"));
		$this->assertEquals(0, $this->eventsDispatched);
	}

	public function testTwoListeners()
	{
		// creating listener
		$this->dispatcher->addListener("unit.test", function($e) {
			$this->eventsDispatched++;
		});
		// creating second listener for the same event
		$this->dispatcher->addListener("unit.test", function($e) {
			$this->eventsDispatched++;
		});

		$this->dispatcher->dispatch($this->event);
		$this->assertEquals(2, $this->eventsDispatched);
	}

	public function testTwoListenersTwoFires()
	{
		// creating listener
		$this->dispatcher->addListener("unit.test", function($e) {
			$this->eventsDispatched++;
		});
		// creating second listener for the same event
		$this->dispatcher->addListener("unit.test", function($e) {
			$this->eventsDispatched++;
		});

		// 2 fires
		$this->dispatcher->dispatch($this->event);
		$this->dispatcher->dispatch($this->event);
		$this->assertEquals(4, $this->eventsDispatched);
	}
}
