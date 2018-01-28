<?php

declare(strict_types = 1);

namespace Stratadox\Hydration\Hydrator;

use Closure;
use ReflectionClass;
use Stratadox\Hydration\Hydrates;
use Stratadox\Hydration\MapsObject;
use Stratadox\Hydration\MapsProperty;
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
    private $mapper;
    private $setter;
    private $object;

    private function __construct(
        MapsObject $mapped,
        ReflectionClass $reflector,
        Closure $setter = null
    ) {
        $this->mapper = $mapped;
        $this->class = $reflector;
        $this->setter = $setter ?: function (string $attribute, $value)
        {
            $this->$attribute = $value;
        };
    }

    public static function fromThis(
        MapsObject $mapped,
        Closure $setter = null
    ) : Hydrates
    {
        return new MappedHydrator($mapped,
            new ReflectionClass($mapped->className()),
            $setter
        );
    }

    public function fromArray(array $data)
    {
        try {
            $this->object = $this->class->newInstanceWithoutConstructor();
            foreach ($this->mapper->properties() as $mapped) {
                $this->write($mapped, $data);
            }
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

    private function write(
        MapsProperty $mapped,
        array $data
    ) : void
    {
        $this->setter->call($this->object,
            $mapped->name(),
            $mapped->value($data, $this->object)
        );
    }
}
