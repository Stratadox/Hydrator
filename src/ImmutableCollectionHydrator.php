<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

use ReflectionException;
use ReflectionMethod;
use Throwable;

/**
 * Hydrates a collection by calling its constructor with squashed array input.
 *
 * @author  Stratadox
 */
final class ImmutableCollectionHydrator implements Hydrator
{
    private function __construct()
    {
    }

    /**
     * Produces a collection hydrator.
     *
     * @return Hydrator A hydrator that calls the constructor through reflection.
     */
    public static function default(): Hydrator
    {
        return new ImmutableCollectionHydrator();
    }

    /** @inheritdoc */
    public function writeTo(object $collection, array $input): void
    {
        try {
            $this->write($collection, $input);
        } catch (Throwable $exception) {
            throw HydrationFailed::encountered($exception, $collection, $input);
        }
    }

    /** @throws ReflectionException */
    private function write(object $collection, array $input): void
    {
        $constructor = new ReflectionMethod($collection, '__construct');
        $constructor->setAccessible(true);
        $constructor->invoke($collection, ...$input);
    }
}
