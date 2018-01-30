<?php

declare(strict_types = 1);

namespace Stratadox\Hydration\Hydrator;

use InvalidArgumentException;
use Stratadox\Hydrator\CouldNotHydrate;

final class CannotDecideOnAHydrator extends InvalidArgumentException implements CouldNotHydrate
{
    public static function withThis(string $hydratorKey) : self
    {
        return new self(sprintf(
            'Invalid class decision key: `%s`.',
            $hydratorKey
        ));
    }

    public static function without(string $decisionKey) : self
    {
        return new self(sprintf(
            'Missing class decision key: `%s`.',
            $decisionKey
        ));
    }
}
