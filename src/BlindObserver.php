<?php

declare(strict_types=1);

namespace Stratadox\Hydrator;

class BlindObserver implements ObservesHydration
{
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
