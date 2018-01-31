<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator;

/**
 * Hydrates an array. Since the input is already array, do nothing, really.
 *
 * @package Stratadox\Hydrate
 * @author Stratadox
 */
final class ArrayHydrator implements Hydrates
{
    /**
     * Creates a new array hydrator.
     *
     * @return self
     */
    public static function create() : self
    {
        return new self;
    }

    /** @inheritdoc */
    public function fromArray(array $input)
    {
        return $input;
    }
}
