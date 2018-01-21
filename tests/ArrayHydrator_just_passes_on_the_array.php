<?php

declare(strict_types=1);

namespace Stratadox\Hydrator\Test;

use PHPUnit\Framework\TestCase;
use StdClass;
use Stratadox\Hydration\Hydrator\ArrayHydrator;

/**
 * @covers \Stratadox\Hydration\Hydrator\ArrayHydrator
 */
class ArrayHydrator_just_passes_on_the_array extends TestCase
{
    /** @scenario */
    function getting_out_what_was_put_in()
    {
        $foo = new StdClass;
        $this->assertSame(
            ['foo' => $foo],
            ArrayHydrator::create()->fromArray(['foo' => $foo])
        );
    }
}
