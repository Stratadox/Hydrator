<?php

declare(strict_types=1);

namespace Stratadox\Hydrator;

use ReflectionClass;
use RuntimeException;
use function sprintf;
use Throwable;

final class HydrationFailed extends RuntimeException implements CouldNotHydrate
{
    public static function encountered(
        Throwable $exception,
        ReflectionClass $class
    ) : self
    {
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
