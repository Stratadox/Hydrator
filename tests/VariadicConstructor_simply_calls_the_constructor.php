<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator\Test;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StdClass;
use Stratadox\Hydration\Hydrator\VariadicConstructor;
use Stratadox\Hydration\Test\Asset\Numbers\CollectionOfIntegers;
use Stratadox\Hydrator\ObservesHydration;

/**
 * @covers \Stratadox\Hydration\Hydrator\VariadicConstructor
 */
class VariadicConstructor_simply_calls_the_constructor extends TestCase
{
    /**
     * Although we can instantiate an ImmutableCollection without calling its
     * constructor, its immutability would prevent us from further hydrating.
     *
     * In such cases, we'll simply call the constructor instead.
     *
     * @scenario
     */
    function instantiating_immutable_collections_through_their_constructor()
    {
        $hydrator = VariadicConstructor::forThe(CollectionOfIntegers::class);

        /** @var CollectionOfIntegers $collection */
        $collection = $hydrator->fromArray([123, 456]);

        $this->assertInstanceOf(CollectionOfIntegers::class, $collection);
        $this->assertSame(123, $collection[0]);
        $this->assertSame(456, $collection->offsetGet(1));
    }

    /** @scenario */
    function notifying_the_observers()
    {
        $emptyObject = new CollectionOfIntegers(1, 2, 3);

        /** @var ObservesHydration|MockObject $observer */
        $observer = $this->createMock(ObservesHydration::class);
        $observer->expects($this->once())->method('hydrating')->with($emptyObject);

        $hydrator = VariadicConstructor::forThe(
            CollectionOfIntegers::class,
            $observer
        );

        $hydrator->fromArray([1, 2, 3]);
    }

}
