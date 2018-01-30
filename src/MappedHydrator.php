<?php

declare(strict_types = 1);

namespace Stratadox\Hydration\Hydrator;

use Closure;
use ReflectionClass;
use Stratadox\Hydration\MapsProperties;
use Stratadox\Hydrator\Hydrates;
use Stratadox\Hydrator\ObservesHydration;
use Throwable;

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
    private $observer;

    private function __construct(
        ReflectionClass $reflector,
        MapsProperties $mapped,
        ObservesHydration $observer,
        ?Closure $setter
    ) {
        $this->class = $reflector;
        $this->properties = $mapped;
        $this->observer = $observer;
        $this->setter = $setter ?: function (string $attribute, $value)
        {
            $this->$attribute = $value;
        };
    }

    public static function forThe(
        string $class,
        MapsProperties $mapped,
        Closure $setter = null,
        ObservesHydration $observer = null
    ) : Hydrates
    {
        return new MappedHydrator(
            new ReflectionClass($class),
            $mapped,
            $observer ?: BlindObserver::add(),
            $setter
        );
    }

    public function fromArray(array $data)
    {
        try {
            $object = $this->class->newInstanceWithoutConstructor();
            $this->observer->hydrating($object);
            $this->properties->writeData($object, $this->setter, $data);
            return $object;
        } catch (Throwable $exception) {
            throw CouldNotMap::encountered($exception, $this->class);
        }
    }
}
