<?php

declare(strict_types=1);

namespace Stratadox\Hydration\Hydrator;

use ReflectionClass;
use RuntimeException;
use function sprintf;
use Stratadox\Hydration\UnmappableInput;
use Throwable;

final class CouldNotMap extends RuntimeException implements UnmappableInput
{
    public static function encountered(
        Throwable $exception,
        ReflectionClass $class
    ) : self
    {
        return new self(
            sprintf('Could not map the class `%s`: %s',
                $class->getName(),
                $exception->getMessage()
            ),
            0,
            $exception
        );
    }
}
