<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

use Closure;
use Stratadox\Instantiator\CannotInstantiateThis;
use Stratadox\Instantiator\Instantiator;
use Stratadox\Instantiator\ProvidesInstances;
use Throwable;

/**
 * Hydrates an object from array input.
 *
 * @package Stratadox\Hydrate
 * @author  Stratadox
 */
final class SimpleHydrator implements Hydrates
{
    private $make;
    private $setter;
    private $observer;

    private function __construct(
        ProvidesInstances $instances,
        ObservesHydration $observer,
        ?Closure $setter
    ) {
        $this->make = $instances;
        $this->observer = $observer;
        $this->setter = $setter ?: function (string $attribute, $value) {
            $this->$attribute = $value;
        };
    }

    /**
     * Creates a new simple hydrator.
     *
     * @param string                 $class    The class to hydrate.
     * @param Closure|null           $setter   The closure that writes the values.
     * @param ObservesHydration|null $observer Object that gets updated with the
     *                                         hydrating instance.
     * @return self                            The hydrator.
     * @throws CannotInstantiateThis           When the class is not instantiable.
     */
    public static function forThe(
        string $class,
        Closure $setter = null,
        ObservesHydration $observer = null
    ): self {
        return new self(
            Instantiator::forThe($class),
            $observer ?: BlindObserver::asDefault(),
            $setter
        );
    }

    /**
     * Creates a new simple hydrator with an instantiator.
     *
     * @param ProvidesInstances      $instantiator The instance provider to use.
     * @param Closure|null           $setter       The closure that writes the
     *                                             values.
     * @param ObservesHydration|null $observer     Object that gets updated with
     *                                             the hydrating instance.
     * @return SimpleHydrator                      The hydrator.
     */
    public static function withInstantiator(
        ProvidesInstances $instantiator,
        Closure $setter = null,
        ObservesHydration $observer = null
    ): self {
        return new self(
            $instantiator,
            $observer ?: BlindObserver::asDefault(),
            $setter
        );
    }

    /** @inheritdoc */
    public function fromArray(array $data)
    {
        try {
            $object = $this->make->instance();
            $this->observer->hydrating($object);
            foreach ($data as $attribute => $value) {
                $this->setter->call($object, $attribute, $value);
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
