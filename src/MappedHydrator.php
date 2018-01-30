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
    private $object;
    private $observer;

    private function __construct(
        ReflectionClass $reflector,
        MapsProperties $mapped,
        ?Closure $setter,
        ?ObservesHydration $observer
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
            new ReflectionClass($class), $mapped, $setter, $observer
        );
    }

    public function fromArray(array $data)
    {
        try {
            $this->object = $this->class->newInstanceWithoutConstructor();
            if ($this->observer) {
                $this->observer->hydrating($this->object);
            }
            $this->properties->writeData($this->object, $this->setter, $data);
            return $this->object;
        } catch (Throwable $exception) {
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
