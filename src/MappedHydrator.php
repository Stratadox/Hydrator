<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator;

use Closure;
use Stratadox\HydrationMapping\MapsProperties;
use Stratadox\Instantiator\CannotInstantiateThis;
use Stratadox\Instantiator\Instantiator;
use Stratadox\Instantiator\ProvidesInstances;
use Throwable;

/**
 * Hydrates an object from mapped array input.
 *
 * @package Stratadox\Hydrate
 * @author Stratadox
 */
final class MappedHydrator implements Hydrates
{
    private $make;
    private $properties;
    private $setter;
    private $observer;

    private function __construct(
        ProvidesInstances $instances,
        MapsProperties $mapped,
        ObservesHydration $observer,
        ?Closure $setter
    ) {
        $this->make = $instances;
        $this->properties = $mapped;
        $this->observer = $observer;
        $this->setter = $setter ?: function (string $attribute, $value) {
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
     * @throws CannotInstantiateThis           When the class is not instantiable.
     */
    public static function forThe(
        string $class,
        MapsProperties $mapped,
        Closure $setter = null,
        ObservesHydration $observer = null
    ): self {
        return new self(
            Instantiator::forThe($class),
            $mapped,
            $observer ?: BlindObserver::add(),
            $setter
        );
    }

    /**
     * Creates a new mapped hydrator with an instantiator.
     *
     * @param ProvidesInstances $instantiator The instance provider to use.
     * @param MapsProperties    $mapped       The mappings for the properties.
     * @param Closure|null           $setter   The closure that writes the values.
     * @param ObservesHydration|null $observer Object that gets updated with the
     *                                         hydrating instance.
     * @return MappedHydrator                 The mapped hydrator.
     */
    public static function withInstantiator(
        ProvidesInstances $instantiator,
        MapsProperties $mapped,
        Closure $setter = null,
        ObservesHydration $observer = null
    ): self {
        return new self(
            $instantiator,
            $mapped,
            $observer ?: BlindObserver::add(),
            $setter
        );
    }

    /** @inheritdoc */
    public function fromArray(array $data)
    {
        try {
            $object = $this->make->instance();
            $this->observer->hydrating($object);
            foreach ($this->properties as $property) {
                $this->setter->call($object,
                    $property->name(),
                    $property->value($data, $object)
                );
            }
            return $object;
        } catch (Throwable $exception) {
            throw HydrationFailed::encountered($exception, $this->make->class());
        }
    }

    /** @inheritdoc */
    public function classFor(array $input): string
    {
        return $this->make->class();
    }
}
