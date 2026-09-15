# SugiPHP Events

A minimal, dependency-light [PSR-14](https://www.php-fig.org/psr/psr-14/) event dispatcher for PHP 8.3+.

It provides:

- `Dispatcher` — a PSR-14 `EventDispatcherInterface` implementation that calls listeners in registration order and stops on [`StoppableEventInterface`](https://www.php-fig.org/psr/psr-14/#22-stoppableeventinterface).
- `ListenerProvider` — a PSR-14 `ListenerProviderInterface` implementation that maps an event class (or any of its parent classes / interfaces) to the listeners registered for it.
- `EventDispatcherTrait` — an optional trait to add dispatching capability to any class without requiring a hard dependency on `Dispatcher` in its constructor.

## Installation

```bash
composer require sugiphp/events ^2.0
```

## Basic usage

```php
use SugiPHP\Events\Dispatcher;
use SugiPHP\Events\ListenerProvider;

$provider = new ListenerProvider();
$provider->addListener(UserRegistered::class, function (UserRegistered $event) {
    // send a welcome email, etc.
});

$dispatcher = new Dispatcher($provider);

$event = new UserRegistered($user);
$dispatcher->dispatch($event);
```

Any PHP object can be used as an event — there is no base `Event` class or interface to extend.

## Registering listeners

`ListenerProvider::addListener()` takes an event class (or interface) name and any PHP `callable`:

```php
$provider->addListener(UserRegistered::class, new SendWelcomeEmail());
$provider->addListener(UserRegistered::class, [$logger, 'logRegistration']);
$provider->addListener(UserRegistered::class, 'notify_admins');
```

Listeners are invoked in the order they were registered. Registering against a parent class or
an interface matches every event that `instanceof` that type:

```php
interface DomainEvent {}
class UserRegistered implements DomainEvent {}

// Fires for UserRegistered and any other DomainEvent implementation.
$provider->addListener(DomainEvent::class, $auditLogger);
```

## Stopping propagation

Events that implement `Psr\EventDispatcher\StoppableEventInterface` can stop remaining listeners
from being called:

```php
use Psr\EventDispatcher\StoppableEventInterface;

class UserRegistered implements StoppableEventInterface
{
    private bool $stopped = false;

    public function isPropagationStopped(): bool
    {
        return $this->stopped;
    }

    public function stopPropagation(): void
    {
        $this->stopped = true;
    }
}
```

Once a listener calls `stopPropagation()`, `Dispatcher::dispatch()` stops calling any further
listeners and returns the event as-is.

## Using the trait

`EventDispatcherTrait` lets a class expose a `dispatch()` method without requiring a `Dispatcher`
to be injected through its constructor. If no dispatcher has been set, `dispatch()` is a no-op
that returns the event unchanged.

```php
use SugiPHP\Events\Dispatcher;
use SugiPHP\Events\EventDispatcherTrait;
use Psr\EventDispatcher\EventDispatcherInterface;

class UserService implements EventDispatcherInterface
{
    use EventDispatcherTrait;
}

$service = new UserService();
$service->setEventDispatcher(new Dispatcher($provider));
$service->dispatch(new UserRegistered($user));
```

## Requirements

- PHP >= 8.3
- [psr/event-dispatcher](https://packagist.org/packages/psr/event-dispatcher) ^1.0

## Testing

```bash
composer test
```

## License

MIT
