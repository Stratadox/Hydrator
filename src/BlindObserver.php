<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator;

/**
 * NullObject for *not* observing the hydration process.
 *
 * @package Stratadox\Hydrate
 * @author Stratadox
 */
final class BlindObserver implements ObservesHydration
{
    /**
     * Creates a new blind observer.
     *
     * @return self
     */
    public static function add() : self
    {
        return new self;
    }

    /** @inheritdoc */
    public function hydrating($theInstance) : void
    {
        // No operation.
    }
}
