<?php

declare(strict_types = 1);

namespace Stratadox\Hydration\Hydrator;

use Closure;
use ReflectionClass;
use Stratadox\Hydration\Hydrates;

/**
 * Hydrates an object from array input.
 *
 * @package Stratadox\Hydrate
 * @author Stratadox
 */
final class SimpleHydrator implements Hydrates
{
    private $class;
    private $setter;

    private function __construct(
        ReflectionClass $reflector,
        Closure $setter = null
    ) {
        $this->class = $reflector;
        $this->setter = $setter ?: function (string $attribute, $value)
        {
            $this->$attribute = $value;
        };
    }

    public static function forThe(
        string $class,
        Closure $setter = null
    ) : SimpleHydrator
    {
        return new static(new ReflectionClass($class), $setter);
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
