<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator\Test;

use PHPUnit\Framework\TestCase;
use stdClass;
use Stratadox\Hydrator\ArrayHydrator;

/**
 * @covers \Stratadox\Hydrator\ArrayHydrator
 */
class ArrayHydrator_just_passes_on_the_array extends TestCase
{
    /** @test */
    function getting_out_what_was_put_in()
    {
        $foo = new stdClass;
        $this->assertSame(
            ['foo' => $foo],
            ArrayHydrator::create()->fromArray(['foo' => $foo])
        );
    }

    /** @test */
    function hydrating_arrays()
    {
        $this->assertSame('array', ArrayHydrator::create()->classFor([]));
    }
}
