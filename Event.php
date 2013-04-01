<?php
/**
 * @package    SugiPHP
 * @subpackage Events
 * @author     Plamen Popov <tzappa@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Events;

class Event
{
	/**
	 * Event name
	 * @var string
	 */
	protected $name;

	/**
	 * Dispatcher who handles event firing
	 * @var \SugiPHP\Events\Dispatcher
	 */
	protected $dispatcher;

	/**
	 * Event constructor.
	 * 
	 * @param string $eventName
	 */
	public function __construct($eventName)
	{
		$this->name = $eventName;
	}

	/**
	 * Gets event name.
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Sets event name.
	 * 
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Gets the Dispatcher that dispatches this Event
	 * 
	 * @return Dispatcher
	 */
	public function getDispatcher()
	{
		return $this->dispatcher;
	}

	/**
	 * Sets the Dispatcher that dispatches this Event
	 * 
	 * @param Dispatcher $dispatcher
	 */
	public function setDispatcher(Dispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}
}
