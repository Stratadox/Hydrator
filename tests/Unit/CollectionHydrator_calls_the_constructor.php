<?php

declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Unit;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Stratadox\Hydrator\CannotHydrate;
use Stratadox\Hydrator\CollectionHydrator;
use Stratadox\Hydrator\Test\Fixture\CollectionOfIntegers;
use Stratadox\Hydrator\Test\Fixture\InconstructibleCollection;

/**
 * @covers \Stratadox\Hydrator\CollectionHydrator
 */
class CollectionHydrator_calls_the_constructor extends TestCase
{
    /** @test */
    function instantiating_immutable_collections_through_their_constructor()
    {
        $hydrator = CollectionHydrator::default();

        /** @var CollectionOfIntegers $collection */
        $collection = (new ReflectionClass(CollectionOfIntegers::class))
            ->newInstanceWithoutConstructor();

        $hydrator->writeTo($collection, [123, 456]);

        $this->assertInstanceOf(CollectionOfIntegers::class, $collection);
        $this->assertSame(123, $collection[0]);
        $this->assertSame(456, $collection->offsetGet(1));
    }

    /** @test */
    function converting_constructor_exceptions_to_CannotHydrate()
    {
        $hydrator = CollectionHydrator::default();
        $class = InconstructibleCollection::class;

        /** @var InconstructibleCollection $collection */
        $collection = (new ReflectionClass(InconstructibleCollection::class))
            ->newInstanceWithoutConstructor();

        $this->expectException(CannotHydrate::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            "Could not hydrate the `$class`: Cannot construct (foo, bar)"
        );

        $hydrator->writeTo($collection, ['foo', 'bar']);
    }
}
