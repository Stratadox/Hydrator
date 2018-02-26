<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator\Test;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydrator\Test\Asset\ExceptionThrower;
use Stratadox\Hydrator\Test\Asset\Numbers\CollectionOfIntegers;
use Stratadox\Hydrator\CouldNotHydrate;
use Stratadox\Hydrator\VariadicConstructor;

/**
 * @covers \Stratadox\Hydrator\VariadicConstructor
 */
class VariadicConstructor_simply_calls_the_constructor extends TestCase
{
    /**
     * Although we can instantiate an ImmutableCollection without calling its
     * constructor, its immutability would prevent us from further hydrating.
     *
     * In such cases, we'll simply call the constructor instead.
     *
     * @test
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

    /** @test */
    function throwing_the_expected_exception_when_things_go_wrong()
    {
        $hydrator = VariadicConstructor::forThe(ExceptionThrower::class);

        ExceptionThrower::setMessage('Original exception message here.');

        $this->expectException(CouldNotHydrate::class);
        $this->expectExceptionMessage(
            'Could not load the class `'.ExceptionThrower::class. '`: Original exception message here.'
        );

        $hydrator->fromArray([]);
    }
}
