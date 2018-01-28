<?php

declare(strict_types=1);

namespace Stratadox\Hydration\Test\Asset\Spy;

use PHPUnit\Framework\Assert;
use ReflectionClass;
use Stratadox\Hydration\Hydrates;

class SpyOnCurrentInstanceAsHydrator extends SpyOnCurrentInstance implements Hydrates
{
    public function currentInstance()
    {
        return (new ReflectionClass($this->expectedClass))
            ->newInstanceWithoutConstructor();
    }

    public function fromArray(array $input)
    {
        Assert::assertInstanceOf($this->expectedClass,
            $this->target->currentInstance()
        );
    }
}
