<?php
/**
 * @package    SugiPHP
 * @subpackage Events
 * @author     Plamen Popov <tzappa@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Events;

interface EventInterface
{
	/**
	 * Sets Event name.
	 * 
	 * @param string $name
	 */
	public function setName($name);
	
	/**
	 * Gets the name of the Event.
	 * 
	 * @return string
	 */
	public function getName();
}
