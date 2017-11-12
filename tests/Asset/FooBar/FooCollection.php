<?php

namespace Stratadox\Hydration\Test\Asset\FooBar;

use Stratadox\ImmutableCollection\ImmutableCollection;

class FooCollection extends ImmutableCollection
{
    public function __construct(Foo ...$foos)
    {
        parent::__construct(...$foos);
    }
}
