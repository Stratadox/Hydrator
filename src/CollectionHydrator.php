<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

use ReflectionException;
use ReflectionMethod as Reflected;
use Throwable;

/**
 * Hydrates a collection by calling its constructor with squashed array input.
 *
 * @package Stratadox\Hydrate
 * @author  Stratadox
 */
final class CollectionHydrator implements Hydrates
{
    private function __construct()
    {
    }

    public static function default(): Hydrates
    {
        return new CollectionHydrator;
    }

    /** @inheritdoc */
    public function writeTo(object $collection, array $input): object
    {
        try {
            $this->write($collection, $input);
        } catch (Throwable $exception) {
            throw HydrationFailed::encountered($exception, $collection);
        }
        return $collection;
    }

    /** @throws ReflectionException */
    private function write(object $collection, array $input): void
    {
        $constructor = new Reflected($collection, '__construct');
        $constructor->setAccessible(true);
        $constructor->invoke($collection, ...$input);
    }
}
