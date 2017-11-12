<?php

namespace Stratadox\Hydration\Hydrator;

use Closure;
use ReflectionClass;
use Stratadox\Hydration\Hydrates;

class SimpleHydrator implements Hydrates
{
    private $class;
    private $setter;

    public function __construct(string $forTheClass, Closure $setter = null)
    {
        $this->class = new ReflectionClass($forTheClass);
        $this->setter = $setter ?: function (string $attribute, $value) {
            $this->$attribute = $value;
        };
    }

    public static function forThe(string $theClass, Closure $setter = null) : SimpleHydrator
    {
        return new static($theClass, $setter);
    }

    public function fromArray(array $data)
    {
        $entity = $this->class->newInstanceWithoutConstructor();
        foreach ($data as $attribute => $value) {
            $this->setter->call($entity, $attribute, $value);
        }
        return $entity;
    }
}
