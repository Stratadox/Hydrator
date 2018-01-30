<?php

declare(strict_types = 1);

namespace Stratadox\Hydration\Hydrator;

use Closure;
use ReflectionClass;
use Stratadox\Hydrator\Hydrates;
use Stratadox\Hydrator\ObservesHydration;

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
    private $observer;

    private function __construct(
        ReflectionClass $reflector,
        ?Closure $setter,
        ?ObservesHydration $observer
    ) {
        $this->class = $reflector;
        $this->observer = $observer;
        $this->setter = $setter ?: function (string $attribute, $value)
        {
            $this->$attribute = $value;
        };
    }

    public static function forThe(
        string $class,
        Closure $setter = null,
        ObservesHydration $observer = null
    ) : Hydrates
    {
        return new SimpleHydrator(
            new ReflectionClass($class), $setter, $observer
        );
    }

    public function fromArray(array $data)
    {
        $object = $this->class->newInstanceWithoutConstructor();
        if ($this->observer) {
            $this->observer->hydrating($object);
        }
        foreach ($data as $attribute => $value) {
            $this->setter->call($object, $attribute, $value);
        }
        return $object;
    }
}
