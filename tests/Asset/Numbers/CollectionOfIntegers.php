<?php

declare(strict_types = 1);

namespace Stratadox\Hydration\Test\Asset\Numbers;

use Stratadox\ImmutableCollection\ImmutableCollection;

class CollectionOfIntegers extends ImmutableCollection
{
    public function __construct(int ...$integers)
    {
        parent::__construct(...$integers);
    }
}
