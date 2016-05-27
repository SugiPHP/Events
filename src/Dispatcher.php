<?php
/**
 * Events Dispatcher.
 *
 * @package SugiPHP.Events
 * @author  Plamen Popov <tzappa@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Events;

class Dispatcher
{
    /**
     * All registered event listeners.
     *
     * @var array
     */
    protected $listeners = array();

    /**
     * Notifies registered for that event callback function
     *
     * @param Event $event
     */
    public function dispatch(EventInterface $event)
    {
        $eventName = $event->getName();
        foreach ($this->getListeners($eventName) as $listener) {
            call_user_func($listener, $event);
        }
    }

    /**
     * Registers an event listener.
     *
     * @param string $eventName
     * @param callable $callback
     */
    public function addListener($eventName, $callback)
    {
        $this->listeners[$eventName][] = $callback;
    }

    /**
     * Gets the listeners for a given event.
     *
     * @param string $eventName
     *
     * @return array
     */
    public function getListeners($eventName)
    {
        if (isset($this->listeners[$eventName])) {
            return $this->listeners[$eventName];
        }

        return array();
    }

    /**
     * Checks event has any registered listeners.
     *
     * @param string $eventName
     *
     * @return boolean
     */
    public function hasListeners($eventName)
    {
        return (boolean) count($this->getListeners($eventName));
    }
}
