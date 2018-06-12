<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

use Closure;
use ReflectionObject;
use Throwable;

/**
 * Hydrates an object from array input.
 *
 * @package Stratadox\Hydrate
 * @author  Stratadox
 */
final class ObjectHydrator implements Hydrates
{
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
     * Faster than a reflective object hydrator, but limited to properties that
     * can be accessed by the instance itself. That means this method cannot be
     * used in the context of inheritance when the parent class has private
     * properties.
     *
     * @return Hydrates A hydrator that uses closure binding to write properties.
     */
    public static function default(): Hydrates
    {
        return new self(null);
    }

    /**
     * Produces an object hydrator with a reflection setter.
     *
     * Slower than the default setter, but useful in the context of inheritance,
     * when some of the properties are private to the parent class and therefore
     * inaccessible through simple closure binding.
     *
     * @return Hydrates A hydrator that uses reflection to write properties.
     */
    public static function reflective(): Hydrates
    {
        return new self((new class {
            public function setter(): Closure
            {
                return function (string $name, $value): void {
                    $object = new ReflectionObject($this);
                    while ($object && !$object->hasProperty($name)) {
                        $object = $object->getParentClass();
                    }
                    // @todo if !object, write as public?
                    $property = $object->getProperty($name);
                    $property->setAccessible(true);
                    $property->setValue($this, $value);
                };
            }
        })->setter());
    }

    /**
     * Produces an object hydrator with a custom setter.
     *
     * @param Closure   $setter The closure that writes the values.
     * @return Hydrates         A hydrator that uses a custom closure to write
     *                          properties.
     */
    public static function using(
        Closure $setter
    ): Hydrates {
        return new self($setter);
    }

    /** @inheritdoc */
    public function writeTo(object $target, array $data): object
    {
        foreach ($data as $attribute => $value) {
            try {
                $this->setter->call($target, $attribute, $value);
            } catch (Throwable $exception) {
                throw HydrationFailed::encountered($exception, $target);
            }
        }
        return $target;
    }
}
