# Events

[![Build Status](https://travis-ci.org/SugiPHP/Events.png)](https://travis-ci.org/SugiPHP/Events)

Observer design pattern-like events system.

## Installation

```shell
composer require sugiphp/events ~1.0
```

## Usage

### Event

Event is a simple object identified by it's unique name. When an event is fired the Event Dispatcher notifies registered
listeners for that particular event name.

### Listener

Any function or method that takes no more than one argument can act as a listener. When an event is fired the dispatcher calls all
registered listeners (functions) one by one.


### Dispatcher

Dispatcher have most significant role in the events systems. All events are fired via the dispatcher. The dispatcher checks for any
listeners that are registered with that event and notifies them.

```php
// create a dispatcher
$dispatcher = new Dispatcher();
// register one or more listeners for one or more events
$dispatcher->addListener("user.login", function ($event) {
    // this function will be executed when an event with name "user.login" is fired
});

// fires an event
$dispatcher->dispatch(new Event("user.login"));
```

### Passing data

All listeners should have only one parameter - the event. If we need to pass additional info to those functions we can transport the date with the Event.

```php

$dispatcher->addListener("user.login", function ($event) {
    // get one property
    echo $event->getParam("id"); // 1
    // get a property as Array
    echo $event["username"]; // "demo"
    // fetch all data
    $event->getParams(); // array("id" => 1, "username" => "demo")
});
$event = new Event("user.login", array("id" => 1, "username" => "demo"));
$dispatcher->dispatch($event);
```
