<?php

declare(strict_types = 1);

namespace Stratadox\Hydration\Hydrator;

use Stratadox\Hydrator\Hydrates;

/**
 * Hydrates an array. Since the input is already array, do nothing, really.
 *
 * @package Stratadox\Hydrate
 * @author Stratadox
 */
final class ArrayHydrator implements Hydrates
{
    public static function create() : Hydrates
    {
        return new ArrayHydrator;
    }

    public function fromArray(array $input)
    {
        return $input;
    }
}
