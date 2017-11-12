<?php

namespace Stratadox\Hydration\Hydrator;

use Stratadox\Hydration\Hydrates;

class VariadicConstructor implements Hydrates
{
    private $class;

    public function __construct(string $forTheClass)
    {
        $this->class = $forTheClass;
    }

    public static function forThe(string $theClass) : VariadicConstructor
    {
        return new static($theClass);
    }

    public function fromArray(array $input)
    {
        $class = $this->class;
        return new $class(...$input);
    }
}
