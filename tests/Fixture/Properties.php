<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Fixture;

use Stratadox\HydrationMapping\MapsProperties;
use Stratadox\HydrationMapping\MapsProperty;
use Stratadox\ImmutableCollection\ImmutableCollection;

final class Properties extends ImmutableCollection implements MapsProperties
{
    public static function use(MapsProperty ...$properties): MapsProperties
    {
        return new Properties(...$properties);
    }

    public function offsetGet($index): MapsProperty
    {
        return parent::offsetGet($index);
    }

    public function current(): MapsProperty
    {
        return parent::current();
    }
}
