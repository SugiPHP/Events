<?php

declare(strict_types=1);

namespace SugiPHP\Events\Tests;

use SugiPHP\Events\Dispatcher;
use SugiPHP\Events\ListenerProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\StoppableEventInterface;

#[CoversClass(Dispatcher::class)]
#[UsesClass(ListenerProvider::class)]
class DispatcherTest extends TestCase
{
    public function testCanCreateDispatcher(): void
    {
        $provider = new ListenerProvider();
        $dispatcher = new Dispatcher($provider);
        $this->assertInstanceOf(Dispatcher::class, $dispatcher);
    }

    public function testDispatchSimpleEvent(): void
    {
        $provider = new ListenerProvider();
        $dispatcher = new Dispatcher($provider);

        $event = new TestEvent("initial");
        $result = $dispatcher->dispatch($event);

        $this->assertSame($event, $result);
        $this->assertSame("initial", $result->getValue());
    }

    public function testDispatchEventWithListeners(): void
    {
        $provider = new ListenerProvider();
        $dispatcher = new Dispatcher($provider);

        $callOrder = [];

        $provider->addListener(TestEvent::class, function (
            TestEvent $event,
        ) use (&$callOrder) {
            $callOrder[] = "listener1";
            $event->setValue($event->getValue() . "-modified1");
        });

        $provider->addListener(TestEvent::class, function (
            TestEvent $event,
        ) use (&$callOrder) {
            $callOrder[] = "listener2";
            $event->setValue($event->getValue() . "-modified2");
        });

        $event = new TestEvent("initial");
        $result = $dispatcher->dispatch($event);

        $this->assertSame($event, $result);
        $this->assertSame("initial-modified1-modified2", $result->getValue());
        $this->assertSame(["listener1", "listener2"], $callOrder);
    }

    public function testDispatchStoppableEvent(): void
    {
        $provider = new ListenerProvider();
        $dispatcher = new Dispatcher($provider);

        $callOrder = [];

        $provider->addListener(TestStoppableEvent::class, function (
            TestStoppableEvent $event,
        ) use (&$callOrder) {
            $callOrder[] = "listener1";
            $event->setValue($event->getValue() . "-modified1");
            $event->stopPropagation();
        });

        $provider->addListener(TestStoppableEvent::class, function (
            TestStoppableEvent $event,
        ) use (&$callOrder) {
            $callOrder[] = "listener2";
            $event->setValue($event->getValue() . "-modified2");
        });

        $event = new TestStoppableEvent("initial");
        $result = $dispatcher->dispatch($event);

        $this->assertSame($event, $result);
        $this->assertSame("initial-modified1", $result->getValue());
        $this->assertSame(["listener1"], $callOrder);
    }

    public function testDispatchStoppableEventAlreadyStopped(): void
    {
        $provider = new ListenerProvider();
        $dispatcher = new Dispatcher($provider);

        $callOrder = [];

        $provider->addListener(TestStoppableEvent::class, function (
            TestStoppableEvent $event,
        ) use (&$callOrder) {
            $callOrder[] = "listener1";
            $event->setValue($event->getValue() . "-modified1");
        });

        $event = new TestStoppableEvent("initial");
        $event->stopPropagation(); // Stop before dispatch

        $result = $dispatcher->dispatch($event);

        $this->assertSame($event, $result);
        $this->assertSame("initial", $result->getValue());
        $this->assertSame([], $callOrder);
    }

    public function testDispatchEventWithNoListeners(): void
    {
        $provider = new ListenerProvider();
        $dispatcher = new Dispatcher($provider);

        $event = new TestEvent("initial");
        $result = $dispatcher->dispatch($event);

        $this->assertSame($event, $result);
        $this->assertSame("initial", $result->getValue());
    }
}

class TestEvent
{
    public function __construct(private string $value) {}

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}

class TestStoppableEvent implements StoppableEventInterface
{
    private bool $propagationStopped = false;

    public function __construct(private string $value) {}

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}
