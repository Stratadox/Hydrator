<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

use ReflectionClass;
use ReflectionObject;
use Throwable;

/**
 * Hydrates an inheriting object from array input.
 *
 * Slower than the default object hydrator, but useful in the context of
 * inheritance, when some of the properties are private to the parent class and
 * therefore inaccessible through simple closure binding.
 * @todo actually, it can be done, by binding the closure with a class scope.
 * @todo find out if that improves performance
 *
 * @package Stratadox\Hydrate
 * @author  Stratadox
 */
final class ReflectiveHydrator implements Hydrates
{
    private function __construct()
    {
    }

    /**
     * Produce a reflective hydrator.
     *
     * @return Hydrates A hydrator that uses reflection to write properties.
     */
    public static function default(): Hydrates
    {
        return new ReflectiveHydrator;
    }

    /** @inheritdoc */
    public function writeTo(object $target, array $data): void
    {
        $object = new ReflectionObject($target);
        foreach ($data as $name => $value) {
            try {
                $this->write($object, $target, $name, $value);
            } catch (Throwable $exception) {
                throw HydrationFailed::encountered($exception, $target);
            }
        }
    }

    private function write(
        ReflectionClass $class,
        object $target,
        string $name,
        $value
    ): void {
        while ($class && !$class->hasProperty($name)) {
            $class = $class->getParentClass();
        }
        if (!$class) {
            $target->$name = $value;
            return;
        }
        $property = $class->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($target, $value);
    }
}
