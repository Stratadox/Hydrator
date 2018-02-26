<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator\Test\Asset;

use Stratadox\HydrationMapping\MapsProperties;
use Stratadox\HydrationMapping\MapsProperty;
use Stratadox\ImmutableCollection\ImmutableCollection;

class Properties extends ImmutableCollection implements MapsProperties
{
    public function __construct(MapsProperty ...$properties)
    {
        parent::__construct(...$properties);
    }

    public function offsetGet($offset) : MapsProperty
    {
        return parent::offsetGet($offset);
    }

    public function current() : MapsProperty
    {
        return parent::current();
    }
}
