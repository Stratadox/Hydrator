<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator;

use ReflectionClass;
use RuntimeException;
use function sprintf;
use Throwable;

/**
 * Notifies the client code that the hydration of an object failed.
 *
 * @package Stratadox\Hydrate
 * @author Stratadox
 */
final class HydrationFailed extends RuntimeException implements CouldNotHydrate
{
    /**
     * Notifies the client code that an exception was encountered during the
     * hydration process.
     *
     * @param Throwable       $exception The exception that was thrown.
     * @param ReflectionClass $class     The class that was being hydrated.
     * @return self                      The new exception to throw.
     */
    public static function encountered(
        Throwable $exception,
        ReflectionClass $class
    ): self {
        return new self(
            sprintf('Could not load the class `%s`: %s',
                $class->getName(),
                $exception->getMessage()
            ),
            0,
            $exception
        );
    }
}
