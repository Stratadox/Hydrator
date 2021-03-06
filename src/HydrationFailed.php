<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

use function get_class;
use RuntimeException;
use function sprintf;
use Throwable;

/**
 * Notifies the client code that the hydration of an object failed.
 *
 * @author  Stratadox
 */
final class HydrationFailed extends RuntimeException implements HydrationFailure
{
    /**
     * Notifies the client code that an exception was encountered during the
     * hydration process.
     *
     * @param Throwable $exception The exception that was thrown.
     * @param object    $target    The object that was being hydrated.
     * @return HydrationFailure    The exception to throw.
     */
    public static function encountered(
        Throwable $exception,
        object $target
    ): HydrationFailure {
        return new self(sprintf(
            'Could not hydrate the `%s`: %s',
            get_class($target),
            $exception->getMessage()
        ), 0, $exception);
    }
}
