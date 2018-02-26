<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator;

use InvalidArgumentException;

/**
 * Notifies the client code that the class decision could not be made.
 *
 * @package Stratadox\Hydrate
 * @author Stratadox
 */
final class CannotDecideOnAHydrator extends InvalidArgumentException implements CouldNotHydrate
{
    /**
     * Notifies the client code that the decision key found in the input data is
     * not a key that was registered with the class mapping.
     *
     * @param string $hydratorKey The key that was found in the input data.
     * @return self               The exception object.
     */
    public static function withThis(string $hydratorKey): self
    {
        return new self(sprintf(
            'Invalid class decision key: `%s`.',
            $hydratorKey
        ));
    }

    /**
     * Notifies the client code that the field in which the decision key was
     * expected, does not exist in the input data.
     *
     * @param string $decisionKey The key that should contain the decision key.
     * @return self               The exception object.
     */
    public static function without(string $decisionKey): self
    {
        return new self(sprintf(
            'Missing class decision key: `%s`.',
            $decisionKey
        ));
    }
}
