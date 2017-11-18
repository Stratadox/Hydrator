<?php

namespace Stratadox\Hydration\Hydrator;

use Closure;
use ReflectionClass;
use Stratadox\Hydration\Hydrates;
use Stratadox\Hydration\MapsObject;
use Stratadox\Hydration\MapsProperty;

class MappedHydrator implements Hydrates
{
    private $class;
    private $mapper;
    private $setter;

    private function __construct(MapsObject $mapped, ReflectionClass $reflector, Closure $setter = null)
    {
        $this->mapper = $mapped;
        $this->class = $reflector;
        $this->setter = $setter ?: function (string $attribute, $value) {
            $this->$attribute = $value;
        };
    }

    public static function fromThis(MapsObject $mapped, Closure $setter = null) : MappedHydrator
    {
        return new static($mapped, new ReflectionClass($mapped->className()), $setter);
    }

    public function fromArray(array $data)
    {
        $object = $this->class->newInstanceWithoutConstructor();
        foreach ($this->mapper->properties() as $mapped) {
            $this->writeTo($object, $mapped, $data);
        }
        return $object;
    }

    private function writeTo($entity, MapsProperty $mapped, array $data) : void
    {
        $this->setter->call($entity, $mapped->name(), $mapped->value($data, $entity));
    }
}
