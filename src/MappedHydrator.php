<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator;

use Closure;
use ReflectionClass;
use Stratadox\HydrationMapping\MapsProperties;
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

    /**
     * Creates a new mapped hydrator for a class.
     *
     * @param string                 $class    The class to hydrate.
     * @param MapsProperties         $mapped   The mappings for the properties.
     * @param Closure|null           $setter   The closure that writes the values.
     * @param ObservesHydration|null $observer Object that gets updated with the
     *                                         hydrating instance.
     * @return self                            The mapped hydrator.
     */
    public static function forThe(
        string $class,
        MapsProperties $mapped,
        Closure $setter = null,
        ObservesHydration $observer = null
    ) : self
    {
        return new self(
            new ReflectionClass($class),
            $mapped,
            $observer ?: BlindObserver::add(),
            $setter
        );
    }

    /** @inheritdoc */
    public function fromArray(array $data)
    {
        try {
            $object = $this->class->newInstanceWithoutConstructor();
            $this->observer->hydrating($object);
            foreach ($this->properties as $property) {
                $this->setter->call($object, $property->name(), $property->value($data));
            }
            return $object;
        } catch (Throwable $exception) {
            throw HydrationFailed::encountered($exception, $this->class);
        }
    }
}
