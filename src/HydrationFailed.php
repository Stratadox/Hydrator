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
    /** @var array */
    private $hydrationData;

    private function __construct(
        string $message,
        int $code,
        Throwable $previous,
        array $data
    ) {
        parent::__construct($message, $code, $previous);
        $this->hydrationData = $data;
    }

    /**
     * Notifies the client code that an exception was encountered during the
     * hydration process.
     *
     * @param Throwable $exception The exception that was thrown.
     * @param object    $target    The object that was being hydrated.
     * @param mixed[]   $data      The hydration data that was supplied.
     * @return HydrationFailure    The exception to throw.
     */
    public static function encountered(
        Throwable $exception,
        object $target,
        array $data
    ): HydrationFailure {
        return new self(sprintf(
            'Could not hydrate the `%s`: %s',
            get_class($target),
            $exception->getMessage()
        ), 0, $exception, $data);
    }

    public function hydrationData(): array
    {
        return $this->hydrationData;
    }
}
