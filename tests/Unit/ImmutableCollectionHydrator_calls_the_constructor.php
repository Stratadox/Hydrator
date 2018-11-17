<?php

declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydrator\CannotHydrate;
use Stratadox\Hydrator\ImmutableCollectionHydrator;
use Stratadox\Hydrator\Test\Data\RandomIntegers;
use Stratadox\Hydrator\Test\Data\TwentyFiveRandomSamples;
use Stratadox\Hydrator\Test\Fixture\CollectionOfIntegers;
use Stratadox\Hydrator\Test\Fixture\InconstructibleCollection;
use function strlen;
use function unserialize;

/**
 * @covers \Stratadox\Hydrator\ImmutableCollectionHydrator
 */
class ImmutableCollectionHydrator_calls_the_constructor extends TestCase
{
    use RandomIntegers, TwentyFiveRandomSamples;

    /**
     * @test
     * @dataProvider integers
     */
    function instantiating_immutable_collections_through_their_constructor(
        int ...$elements
    ) {
        $hydrator = ImmutableCollectionHydrator::default();

        $collection = CollectionOfIntegers::empty();

        $hydrator->writeTo($collection, $elements);

        foreach ($elements as $position => $expected) {
            $this->assertSame($expected, $collection[$position]);
            $this->assertSame($expected, $collection->offsetGet($position));
        }
    }

    /** @test */
    function converting_constructor_exceptions_to_CannotHydrate()
    {
        $hydrator = ImmutableCollectionHydrator::default();
        $class = InconstructibleCollection::class;

        /** @var InconstructibleCollection $collection */
        $collection = unserialize(sprintf(
            'O:%d:"%s":0:{}',
            strlen($class),
            $class
        ));

        $this->expectException(CannotHydrate::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            "Could not hydrate the `$class`: Cannot construct (foo, bar)"
        );

        $hydrator->writeTo($collection, ['foo', 'bar']);
    }
}
