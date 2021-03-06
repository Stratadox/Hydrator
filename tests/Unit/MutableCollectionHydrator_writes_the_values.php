<?php

declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydrator\HydrationFailure;
use Stratadox\Hydrator\MutableCollectionHydrator;
use Stratadox\Hydrator\Test\Data\RandomIntegers;
use Stratadox\Hydrator\Test\Data\TwentyFiveRandomSamples;
use Stratadox\Hydrator\Test\Fixture\ArrayObjectWithNumbers;

class MutableCollectionHydrator_writes_the_values extends TestCase
{
    use RandomIntegers, TwentyFiveRandomSamples;

    /**
     * @test
     * @dataProvider integers
     */
    function instantiating_immutable_collections_through_their_constructor(
        int ...$elements
    ) {
        $hydrator = MutableCollectionHydrator::default();

        $collection = ArrayObjectWithNumbers::empty();

        $hydrator->writeTo($collection, $elements);

        foreach ($elements as $position => $expected) {
            self::assertSame($expected, $collection[$position]);
            self::assertSame($expected, $collection->offsetGet($position));
        }
    }

    /** @test */
    function converting_constructor_exceptions_to_CannotHydrate()
    {
        $hydrator = MutableCollectionHydrator::default();
        $class = ArrayObjectWithNumbers::class;

        $collection = ArrayObjectWithNumbers::empty();

        $this->expectException(HydrationFailure::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            "Could not hydrate the `$class`: Input must be numeric."
        );

        $hydrator->writeTo($collection, ['foo', 'bar']);
    }
}
