<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

use RuntimeException;
use function sprintf as withMessage;
use Throwable;

/**
 * Notifies the client code that the hydration of an object failed.
 *
 * @package Stratadox\Hydrate
 * @author  Stratadox
 */
final class HydrationFailed extends RuntimeException implements CannotHydrate
{
    /**
     * Notifies the client code that an exception was encountered during the
     * hydration process.
     *
     * @param Throwable $exception The exception that was thrown.
     * @param string    $class     The class that was being hydrated.
     * @return CannotHydrate       The new exception to throw.
     */
    public static function encountered(
        Throwable $exception,
        string $class
    ): CannotHydrate {
        return new HydrationFailed(withMessage(
            'Could not load the class `%s`: %s',
            $class,
            $exception->getMessage()
        ), 0, $exception);
    }
}
