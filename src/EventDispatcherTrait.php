<?php

declare(strict_types=1);

namespace SugiPHP\Events;

trait EventDispatcherTrait
{
    private Dispatcher $dispatcher;

    public function setEventDispatcher(Dispatcher $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    public function dispatch(object $event): object
    {
        if (isset($this->dispatcher)) {
            return $this->dispatcher->dispatch($event);
        }
        return $event;
    }
}
