<?php

declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Unit;

use function array_map;
use function implode;
use PHPUnit\Framework\TestCase;
use function random_int;
use function range;
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
    private const TESTS = 25;

    /**
     * @test
     * @dataProvider integers
     */
    function instantiating_immutable_collections_through_their_constructor(
        int ...$elements
    ) {
        $hydrator = CollectionHydrator::default();

        /** @var CollectionOfIntegers $collection */
        $collection = (new ReflectionClass(CollectionOfIntegers::class))
            ->newInstanceWithoutConstructor();

        $hydrator->writeTo($collection, $elements);

        $this->assertInstanceOf(CollectionOfIntegers::class, $collection);
        foreach ($elements as $position => $expected) {
            $this->assertSame($expected, $collection[$position]);
            $this->assertSame($expected, $collection->offsetGet($position));
        }
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

    public function integers(): array
    {
        $sets = [];
        for ($i = self::TESTS; $i > 0; --$i) {
            $integers = array_map(function (): int {
                return random_int(-1000, 1000);
            }, range(0, random_int(1, 10)));
            $sets[implode(',', $integers)] = $integers;
        }
        return $sets;
    }
}