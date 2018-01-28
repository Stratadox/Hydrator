<?php

declare(strict_types = 1);

namespace Stratadox\Hydration\Hydrator;

use Closure;
use ReflectionClass;
use Stratadox\Hydration\Hydrates;
use Stratadox\Hydration\MapsProperties;
use Stratadox\Hydration\UnmappableInput;

/**
 * Hydrates an object from mapped array input.
 *
 * @package Stratadox\Hydrate
 * @author Stratadox
 */
final class MappedHydrator implements Hydrates
{
    private $class;
    private $properties;
    private $setter;
    private $object;

    private function __construct(
        ReflectionClass $reflector,
        MapsProperties $mapped,
        Closure $setter = null
    ) {
        $this->class = $reflector;
        $this->properties = $mapped;
        $this->setter = $setter ?: function (string $attribute, $value)
        {
            $this->$attribute = $value;
        };
    }

    public static function forThe(
        string $class,
        MapsProperties $mapped,
        Closure $setter = null
    ) : Hydrates
    {
        return new MappedHydrator(new ReflectionClass($class), $mapped, $setter);
    }

    public function fromArray(array $data)
    {
        try {
            $this->object = $this->class->newInstanceWithoutConstructor();
            $this->properties->writeData($this->object, $this->setter, $data);
            return $this->object;
        } catch (UnmappableInput $exception) {
            throw CouldNotMap::encountered($exception, $this->class);
        } finally {
            $this->object = null;
        }
    }

    public function currentInstance()
    {
        return $this->object;
    }
}
