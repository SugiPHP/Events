<?php
/**
 * Event class. The dispatcher dispatches events of this class.
 *
 * @package SugiPHP.Events
 * @author  Plamen Popov <tzappa@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Events;

class Event implements EventInterface, \ArrayAccess
{
    /**
     * Event name.
     *
     * @var string
     */
    protected $name;

    /**
     * Dispatcher who handles event firing.
     *
     * @var \SugiPHP\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * Some parameters to store in the event.
     *
     * @var array
     */
    protected $params;

    /**
     * Event constructor.
     *
     * @param string $eventName
     * @param array $params
     */
    public function __construct($eventName, array $params = array())
    {
        $this->name = $eventName;
        $this->params = $params;
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
     * Bind some parameters to the Event.
     *
     * @param array $params
     *
     * @return Event
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Return all parameters binded to the Event.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Binds a param to the Event.
     *
     * @param string $name
     * @param mixed $value
     *
     * @return Event
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    /**
     * Return named parameter binded to the Event.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getParam($name)
    {
        return isset($this->params[$name]) ? $this->params[$name] : null;
    }

    /**
     * Returns true if the event has and entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasParam($name)
    {
        return isset($this->params[$name]);
    }

    /**
     * Method needed to implement \ArrayAccess
     *
     * @see http://php.net/manual/en/arrayaccess.offsetset.php
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->params[] = $value;
        } else {
            $this->params[$offset] = $value;
        }
    }

    /**
     * Method needed to implement \ArrayAccess
     *
     * @see http://php.net/manual/en/arrayaccess.offsetexists.php
     */
    public function offsetExists($offset)
    {
        return isset($this->params[$offset]);
    }

    /**
     * Method needed to implement \ArrayAccess
     *
     * @see http://php.net/manual/en/arrayaccess.offsetunset.php
     */
    public function offsetUnset($offset)
    {
        unset($this->params[$offset]);
    }

    /**
     * Method needed to implement \ArrayAccess
     *
     * @see http://php.net/manual/en/arrayaccess.offsetget.php
     */
    public function offsetGet($offset)
    {
        return isset($this->params[$offset]) ? $this->params[$offset] : null;
    }
}
