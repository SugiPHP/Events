<?php

declare(strict_types=1);

namespace SugiPHP\Events\Tests;

use SugiPHP\Events\ListenerProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ListenerProvider::class)]
class ListenerProviderTest extends TestCase
{
    public function testCanCreateProvider(): void
    {
        $provider = new ListenerProvider();
        $this->assertInstanceOf(ListenerProvider::class, $provider);
    }

    public function testGetListenersForEventWithNoListeners(): void
    {
        $provider = new ListenerProvider();
        $event = new TestEventForProvider("test");

        $listeners = $provider->getListenersForEvent($event);

        $this->assertSame([], iterator_to_array($listeners));
    }

    public function testAddSingleListener(): void
    {
        $provider = new ListenerProvider();
        $listenerCalled = false;

        $listener = function (TestEventForProvider $event) use (
            &$listenerCalled,
        ) {
            $listenerCalled = true;
        };

        $provider->addListener(TestEventForProvider::class, $listener);

        $event = new TestEventForProvider("test");
        $listeners = $provider->getListenersForEvent($event);

        $listenersArray = iterator_to_array($listeners);
        $this->assertCount(1, $listenersArray);

        // Call the listener to verify it works
        $listenersArray[0]($event);
        $this->assertTrue($listenerCalled);
    }

    public function testAddMultipleListenersForSameEvent(): void
    {
        $provider = new ListenerProvider();
        $callOrder = [];

        $listener1 = function (TestEventForProvider $event) use (&$callOrder) {
            $callOrder[] = "listener1";
        };

        $listener2 = function (TestEventForProvider $event) use (&$callOrder) {
            $callOrder[] = "listener2";
        };

        $provider->addListener(TestEventForProvider::class, $listener1);
        $provider->addListener(TestEventForProvider::class, $listener2);

        $event = new TestEventForProvider("test");
        $listeners = $provider->getListenersForEvent($event);

        $listenersArray = iterator_to_array($listeners);
        $this->assertCount(2, $listenersArray);

        // Call both listeners to verify order
        foreach ($listenersArray as $listener) {
            $listener($event);
        }

        $this->assertSame(["listener1", "listener2"], $callOrder);
    }

    public function testAddListenersForDifferentEvents(): void
    {
        $provider = new ListenerProvider();

        $listener1 = function (TestEventForProvider $event) {};
        $listener2 = function (AnotherTestEvent $event) {};

        $provider->addListener(TestEventForProvider::class, $listener1);
        $provider->addListener(AnotherTestEvent::class, $listener2);

        $event1 = new TestEventForProvider("test");
        $listeners1 = $provider->getListenersForEvent($event1);
        $this->assertCount(1, iterator_to_array($listeners1));

        $event2 = new AnotherTestEvent();
        $listeners2 = $provider->getListenersForEvent($event2);
        $this->assertCount(1, iterator_to_array($listeners2));
    }

    public function testGetListenersForEventUsesExactClassName(): void
    {
        $provider = new ListenerProvider();

        $listener = function (TestEventForProvider $event) {};
        $provider->addListener(TestEventForProvider::class, $listener);

        // Should find listeners for exact class
        $event1 = new TestEventForProvider("test");
        $listeners1 = $provider->getListenersForEvent($event1);
        $this->assertCount(1, iterator_to_array($listeners1));

        // Should not find listeners for different class
        $event2 = new AnotherTestEvent();
        $listeners2 = $provider->getListenersForEvent($event2);
        $this->assertCount(0, iterator_to_array($listeners2));
    }

    public function testCanAddCallableAsListener(): void
    {
        $provider = new ListenerProvider();

        // Test with closure
        $closure = function (TestEventForProvider $event) {
            return "closure";
        };
        $provider->addListener(TestEventForProvider::class, $closure);

        // Test with callable array
        $callableArray = [$this, "helperListenerMethod"];
        $provider->addListener(TestEventForProvider::class, $callableArray);

        // Test with invokable object
        $invokable = new InvokableListener();
        $provider->addListener(TestEventForProvider::class, $invokable);

        $event = new TestEventForProvider("test");
        $listeners = $provider->getListenersForEvent($event);

        $this->assertCount(3, iterator_to_array($listeners));
    }

    public function helperListenerMethod(): string
    {
        return "method";
    }
}

class TestEventForProvider
{
    public function __construct(private string $value) {}

    public function getValue(): string
    {
        return $this->value;
    }
}

class AnotherTestEvent {}

class InvokableListener
{
    public function __invoke(TestEventForProvider $event): string
    {
        return "invokable";
    }
}
