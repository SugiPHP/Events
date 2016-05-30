<?php
/**
 * Tests for Events and Dispatcher classes
 *
 * @package SugiPHP.Events
 * @author  Plamen Popov <tzappa@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Events;

use SugiPHP\Events\Dispatcher;
use SugiPHP\Events\Event;
use PHPUnit_Framework_TestCase;

class EventsTest extends PHPUnit_Framework_TestCase
{

    public $eventsDispatched;
    public $dispatcher;
    public $event;

    public function setUp()
    {
        $this->eventsDispatched = 0;
        $this->dispatcher = new Dispatcher();
        $this->event = new Event("unit.test");
    }

    public function testOneListener()
    {
        // creating listener
        $this->dispatcher->addListener("unit.test", function ($e) {
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
        $this->dispatcher->addListener("unit.test", function ($e) {
            $this->eventsDispatched++;
        });

        $this->dispatcher->dispatch(new Event("unit.test2"));
        $this->assertEquals(0, $this->eventsDispatched);
    }

    public function testTwoListeners()
    {
        // creating listener
        $this->dispatcher->addListener("unit.test", function ($e) {
            $this->eventsDispatched++;
        });
        // creating second listener for the same event
        $this->dispatcher->addListener("unit.test", function ($e) {
            $this->eventsDispatched++;
        });

        $this->dispatcher->dispatch($this->event);
        $this->assertEquals(2, $this->eventsDispatched);
    }

    public function testTwoListenersTwoFires()
    {
        // creating listener
        $this->dispatcher->addListener("unit.test", function ($e) {
            $this->eventsDispatched++;
        });
        // creating second listener for the same event
        $this->dispatcher->addListener("unit.test", function ($e) {
            $this->eventsDispatched++;
        });

        // 2 fires
        $this->dispatcher->dispatch($this->event);
        $this->dispatcher->dispatch($this->event);
        $this->assertEquals(4, $this->eventsDispatched);
    }

    public function testEventParams()
    {
        // creating listener
        $this->dispatcher->addListener("unit.test", function ($e) {
            $this->eventsDispatched++;
            $this->assertSame("unit.test", $e->getName());
            $params = $e->getParams();
            $this->assertContains("foo", $params);
            $this->assertContains("foobar", $params);
            $this->assertArrayHasKey("bar", $params);
            $this->assertEquals("foobar", $params["bar"]);
        });

        // firing event
        $event = new Event("unit.test", array("foo", "bar" => "foobar"));
        $this->dispatcher->dispatch($event);
        // check that the event was dispatched
        $this->assertEquals(1, $this->eventsDispatched);
    }

    public function testHasEventListener()
    {
        // creating listener
        $this->dispatcher->addListener("unit.test", function ($e) {
            return true;
        });

        $this->assertTrue($this->dispatcher->hasListeners("unit.test"));
        $this->assertFalse($this->dispatcher->hasListeners("foo"));
    }

    public function testGetEventParam()
    {
        $event = new Event("unit.test", array("foo", "bar" => "foobar"));
        $this->assertEquals("foobar", $event->getParam("bar"));
    }

    public function testGetEventParams()
    {
        $params = array("foo", "bar" => "foobar");
        $event = new Event("unit.test", $params);
        $this->assertEquals($params, $event->getParams());
    }

    public function testEventHasParam()
    {
        $params = array("user" => "demo");
        $event = new Event("unit.test", $params);
        $this->assertTrue($event->hasParam("user"));
        $this->assertFalse($event->hasParam("admin"));
    }

    public function testArrayAccessToParams()
    {
        $params = array("username" => "demo", "id" => 1);
        $event = new Event("user.login", $params);
        $this->assertEquals("demo", $event["username"]);
        $this->assertEquals(1, $event["id"]);
        $this->assertTrue(isset($event["username"]));
        $this->assertFalse(isset($event["state"]));
    }

    public function testSetParam()
    {
        $event = new Event("user.login");
        $event->setParam("username", "demo");
        $this->assertSame("demo", $event->getParam("username"));
    }

    public function testReadmeExample()
    {
        $this->dispatcher->addListener("user.login", function ($event) {
            if ("mike" == $event["username"]) {
                // array access
                $event["is_admin"] = true;
            } else {
                // using setParam() method
                $event->setParam("is_admin", false);
            }
        });

        $this->dispatcher->addListener("user.login", function ($event) {
            $this->assertTrue($event["is_admin"]);
        });

        $event = new Event("user.login", array("username" => "mike"));
        $this->dispatcher->dispatch($event);
    }
}
