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

    /**
     * Produces a collection hydrator.
     *
     * @return Hydrates A hydrator that calls the constructor through reflection.
     */
    public static function default(): Hydrates
    {
        return new CollectionHydrator;
    }

    /** @inheritdoc */
    public function writeTo(object $collection, array $input): void
    {
        try {
            $this->write($collection, $input);
        } catch (Throwable $exception) {
            throw HydrationFailed::encountered($exception, $collection);
        }
    }

    /** @throws ReflectionException */
    private function write(object $collection, array $input): void
    {
        $constructor = new Reflected($collection, '__construct');
        $constructor->setAccessible(true);
        $constructor->invoke($collection, ...$input);
    }
}
