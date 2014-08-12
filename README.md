Events
======

[![Build Status](https://travis-ci.org/SugiPHP/Events.png)](https://travis-ci.org/SugiPHP/Events)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SugiPHP/Events/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SugiPHP/Events/?branch=master)


Observer design pattern-like events system.

Event
-----

Event is a simple object identified by it's unique name. When an event is fired the Event Dispatcher notifies registered
listeners for that particular event name.

Listener
--------

Any function or method that takes no more than one argument can act as a listener. When an event is fired the dispatcher calls all
registered listeners (functions) one by one.


Dispatcher
----------

Dispatcher have most significant role in the events systems. All events are fired via the dispatcher. The dispatcher checks for any
listeners that are registered with that event and notifies them.

```php
<?php
	// create a dispatcher
	$dispatcher = new Dispatcher();
	// register one or more listeners for one or more events
	$dispatcher->addListener("user.login", function ($event) {
		// this function will be executed when an event with name "user.login" is fired
	});

	// fires an event
	$dispatcher->dispatch(new Event("user.login"));
?>
```
