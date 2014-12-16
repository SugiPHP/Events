<?php
/**
 * Events Interface.
 *
 * @package SugiPHP.Events
 * @author  Plamen Popov <tzappa@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Events;

interface EventInterface
{

	/**
	 * Gets the name of the Event.
	 *
	 * @return string
	 */
	public function getName();
}
