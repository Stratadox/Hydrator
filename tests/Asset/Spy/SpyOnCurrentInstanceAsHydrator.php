<?php

declare(strict_types=1);

namespace Stratadox\Hydration\Test\Asset\Spy;

use PHPUnit\Framework\Assert;
use ReflectionClass;
use Stratadox\Hydration\Hydrates;

class SpyOnCurrentInstanceAsHydrator implements Hydrates
{
    private $expectedClass;
    private $target;

    public function __construct(string $expectedClass, ?Hydrates $target)
    {
        $this->expectedClass = $expectedClass;
        $this->target = $target;
    }

    public static function expectThe(string $class, Hydrates $target = null)
    {
        return new self($class, $target);
    }

    public function onThe(Hydrates $target)
    {
        $this->target = $target;
    }

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
