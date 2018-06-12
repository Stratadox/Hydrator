<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

use ReflectionClass;
use ReflectionObject;

/**
 * Hydrates an inheriting object from array input.
 *
 * Slower than the default object hydrator, but useful in the context of
 * inheritance, when some of the properties are private to the parent class and
 * therefore inaccessible through simple closure binding.
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
            $this->write($object, $target, $name, $value);
        }
    }

    private function write(
        ReflectionClass $object,
        object $target,
        string $name,
        $value
    ): void {
        while ($object && !$object->hasProperty($name)) {
            $object = $object->getParentClass();
        }
        // @todo if !object, write as public?
        $property = $object->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($target, $value);
    }
}
