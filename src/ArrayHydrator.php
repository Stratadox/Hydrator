<?php

declare(strict_types = 1);

namespace Stratadox\Hydration\Hydrator;

use Stratadox\Hydration\Hydrates;

class ArrayHydrator implements Hydrates
{
    public static function create() : Hydrates
    {
        return new self;
    }

    public function fromArray(array $input)
    {
        return $input;
    }
}
