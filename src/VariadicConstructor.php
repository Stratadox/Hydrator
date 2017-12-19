<?php

declare(strict_types = 1);

namespace Stratadox\Hydration\Hydrator;

use Stratadox\Hydration\Hydrates;

/**
 * Hydrates an object by calling its constructor with squashed array input.
 *
 * @package Stratadox\Hydrate
 * @author Stratadox
 */
class VariadicConstructor implements Hydrates
{
    private $class;

    private function __construct(string $forTheClass)
    {
        $this->class = $forTheClass;
    }

    public static function forThe(string $class) : VariadicConstructor
    {
        return new static($class);
    }

    public function fromArray(array $input)
    {
        $class = $this->class;
        return new $class(...$input);
    }
}
