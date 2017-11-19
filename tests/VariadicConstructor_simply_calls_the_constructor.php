<?php

declare(strict_types=1);

namespace Stratadox\Hydrator\Test;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydration\Hydrator\VariadicConstructor;
use Stratadox\Hydration\Test\Asset\Numbers\CollectionOfIntegers;

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

}
