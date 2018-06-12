<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

use function get_class as theClassOfThe;
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
     * @param object    $target    The object that was being hydrated.
     * @return CannotHydrate       The exception to throw.
     */
    public static function encountered(
        Throwable $exception,
        object $target
    ): CannotHydrate {
        return new HydrationFailed(withMessage(
            'Could not hydrate the `%s`: %s',
            theClassOfThe($target),
            $exception->getMessage()
        ), 0, $exception);
    }
}
