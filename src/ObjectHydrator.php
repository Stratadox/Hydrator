<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

use Closure;
use Throwable;

/**
 * Hydrator an object from array input.
 *
 * Faster than a reflective object hydrator, but limited to properties that
 * can be accessed by the instance itself. That means this method cannot be
 * used in the context of inheritance when the parent class has private
 * properties.
 *
 * @package Stratadox\Hydrate
 * @author  Stratadox
 */
final class ObjectHydrator implements Hydrator
{
    /** @var Closure */
    private $setter;

    private function __construct(
        ?Closure $setter
    ) {
        $this->setter = $setter ?: function (string $attribute, $value): void {
            $this->$attribute = $value;
        };
    }

    /**
     * Produces an object hydrator with a default setter.
     *
     * @return Hydrator A hydrator that uses closure binding to write properties.
     */
    public static function default(): Hydrator
    {
        return new self(null);
    }

    /**
     * Produces an object hydrator with a custom setter.
     *
     * @param Closure   $setter The closure that writes the values.
     * @return Hydrator         A hydrator that uses a custom closure to write
     *                          properties.
     */
    public static function using(
        Closure $setter
    ): Hydrator {
        return new self($setter);
    }

    /** @inheritdoc */
    public function writeTo(object $target, array $input): void
    {
        foreach ($input as $attribute => $value) {
            try {
                $this->setter->call($target, $attribute, $value);
            } catch (Throwable $exception) {
                throw HydrationFailed::encountered($exception, $target);
            }
        }
    }
}
