<?php

declare(strict_types=1);

namespace Stratadox\Hydration\Test\Asset\Spy;

use Stratadox\Hydration\Hydrates;

abstract class SpyOnCurrentInstance
{
    protected $expectedClass;
    protected $target;

    public function __construct(string $expectedClass, ?Hydrates $target)
    {
        $this->expectedClass = $expectedClass;
        $this->target = $target;
    }

    public static function expectThe(string $class, Hydrates $target = null) : self
    {
        return new static($class, $target);
    }

    public function onThe(Hydrates $target) : void
    {
        $this->target = $target;
    }
}
