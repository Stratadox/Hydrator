<?php

declare(strict_types=1);

namespace Stratadox\Hydration\Test\Asset\Spy;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydration\MapsProperty;

class SpyOnCurrentInstanceAsPropertyMapping extends SpyOnCurrentInstance implements MapsProperty
{
    public function name() : string
    {
        return '';
    }

    public function value(array $data, $owner = null)
    {
        TestCase::assertInstanceOf($this->expectedClass,
            $this->target->currentInstance()
        );
    }
}
