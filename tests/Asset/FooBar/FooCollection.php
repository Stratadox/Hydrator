<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Asset\FooBar;

use Stratadox\ImmutableCollection\ImmutableCollection;

class FooCollection extends ImmutableCollection
{
    public function __construct(Foo ...$foos)
    {
        parent::__construct(...$foos);
    }
}
