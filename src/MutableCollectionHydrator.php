<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

use Throwable;

/**
 * Hydrates a collection by calling its constructor with squashed array input.
 *
 * @author  Stratadox
 */
final class MutableCollectionHydrator implements Hydrator
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
        return new self();
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

    private function write(object $collection, array $input): void
    {
        foreach ($input as $key => $value) {
            $collection[$key] = $value;
        }
    }
}
