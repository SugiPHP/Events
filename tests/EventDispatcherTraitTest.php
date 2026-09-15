<?php

declare(strict_types=1);

namespace SugiPHP\Events\Tests;

use SugiPHP\Events\Dispatcher;
use SugiPHP\Events\EventDispatcherTrait;
use SugiPHP\Events\ListenerProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EventDispatcherTrait::class)]
#[UsesClass(Dispatcher::class)]
#[UsesClass(ListenerProvider::class)]
class EventDispatcherTraitTest extends TestCase
{
    public function testCanUseEventDispatcherTrait(): void
    {
        $object = new TestClassWithTrait();
        $this->assertInstanceOf(TestClassWithTrait::class, $object);
    }

    public function testSetEventDispatcher(): void
    {
        $provider = new ListenerProvider();
        $dispatcher = new Dispatcher($provider);
        $object = new TestClassWithTrait();

        $object->setEventDispatcher($dispatcher);

        // We can't directly test the private property, but we can test the behavior
        $event = new TraitTestEvent("test");
        $result = $object->dispatch($event);

        $this->assertSame($event, $result);
    }

    public function testDispatchWithoutSettingDispatcher(): void
    {
        $object = new TestClassWithTrait();

        $event = new TraitTestEvent("test");
        $result = $object->dispatch($event);

        $this->assertSame($event, $result);
    }

    public function testDispatchWithDispatcher(): void
    {
        $provider = new ListenerProvider();
        $dispatcher = new Dispatcher($provider);
        $object = new TestClassWithTrait();

        $listenerCalled = false;
        $provider->addListener(TraitTestEvent::class, function (
            TraitTestEvent $event,
        ) use (&$listenerCalled) {
            $listenerCalled = true;
            $event->setValue($event->getValue() . "-modified");
        });

        $object->setEventDispatcher($dispatcher);

        $event = new TraitTestEvent("initial");
        $result = $object->dispatch($event);

        $this->assertSame($event, $result);
        $this->assertSame("initial-modified", $result->getValue());
        $this->assertTrue($listenerCalled);
    }

    public function testDispatchReturnsOriginalEventReference(): void
    {
        $provider = new ListenerProvider();
        $dispatcher = new Dispatcher($provider);
        $object = new TestClassWithTrait();

        $provider->addListener(TraitTestEvent::class, function (
            TraitTestEvent $event,
        ) {
            $event->setValue("modified");
        });

        $object->setEventDispatcher($dispatcher);

        $event = new TraitTestEvent("initial");
        $result = $object->dispatch($event);

        // Should be the exact same object reference
        $this->assertSame($event, $result);
        $this->assertSame("modified", $event->getValue());
        $this->assertSame("modified", $result->getValue());
    }

    public function testMultipleDispatchCalls(): void
    {
        $provider = new ListenerProvider();
        $dispatcher = new Dispatcher($provider);
        $object = new TestClassWithTrait();

        $callCount = 0;
        $provider->addListener(TraitTestEvent::class, function (
            TraitTestEvent $event,
        ) use (&$callCount) {
            $callCount++;
            $event->setValue($event->getValue() . "-" . $callCount);
        });

        $object->setEventDispatcher($dispatcher);

        $event1 = new TraitTestEvent("first");
        $result1 = $object->dispatch($event1);

        $event2 = new TraitTestEvent("second");
        $result2 = $object->dispatch($event2);

        $this->assertSame("first-1", $result1->getValue());
        $this->assertSame("second-2", $result2->getValue());
        $this->assertSame(2, $callCount);
    }
}

class TestClassWithTrait
{
    use EventDispatcherTrait;
}

class TraitTestEvent
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
