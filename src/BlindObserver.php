<?php

declare(strict_types=1);

namespace Stratadox\Hydration\Hydrator;

use Stratadox\Hydrator\ObservesHydration;

class BlindObserver implements ObservesHydration
{
    public static function add() : self
    {
        return new self;
    }

    public function hydrating($theInstance) : void
    {
        // No operation.
    }
}
