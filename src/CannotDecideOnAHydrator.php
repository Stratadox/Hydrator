<?php

declare(strict_types=1);

namespace Stratadox\Hydration\Hydrator;

use InvalidArgumentException;
use Stratadox\Hydration\UnmappableInput;

class CannotDecideOnAHydrator extends InvalidArgumentException implements UnmappableInput
{
    public static function withThis(string $hydratorKey) : UnmappableInput
    {
        return new static(sprintf(
            'Invalid class decision key: `%s`.',
            $hydratorKey
        ));
    }

    public static function without(string $decisionKey) : UnmappableInput
    {
        return new static(sprintf(
            'Missing class decision key: `%s`.',
            $decisionKey
        ));
    }
}
