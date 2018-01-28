<?php

declare(strict_types=1);

namespace Stratadox\Hydration\Test\Asset\Spy;

use Closure;
use PHPUnit\Framework\TestCase;
use Stratadox\Hydration\MapsProperties;

class SpyOnCurrentInstanceAsPropertyMapping extends SpyOnCurrentInstance implements MapsProperties
{
    public function writeData($object, Closure $setter, array $data) : void
    {
        TestCase::assertInstanceOf($this->expectedClass,
            $this->target->currentInstance()
        );
    }
}
