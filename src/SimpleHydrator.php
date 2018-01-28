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
    private $object;

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
    ) : Hydrates
    {
        return new SimpleHydrator(new ReflectionClass($class), $setter);
    }

    public function fromArray(array $data)
    {
        try {
            $this->object = $this->class->newInstanceWithoutConstructor();
            foreach ($data as $attribute => $value) {
                $this->setter->call($this->object, $attribute, $value);
            }
            return $this->object;
        } finally {
            $this->object = null;
        }
    }

    public function currentInstance()
    {
        return $this->object;
    }
}
