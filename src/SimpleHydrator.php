<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator;

use Closure;
use ReflectionClass;
use Throwable;

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
        ObservesHydration $observer,
        ?Closure $setter
    ) {
        $this->class = $reflector;
        $this->observer = $observer;
        $this->setter = $setter ?: function (string $attribute, $value)
        {
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
     * @return self                            The mapped hydrator.
     */
    public static function forThe(
        string $class,
        Closure $setter = null,
        ObservesHydration $observer = null
    ): self {
        return new self(
            new ReflectionClass($class),
            $observer ?: BlindObserver::add(),
            $setter
        );
    }

    public function fromArray(array $data)
    {
        try {
            $object = $this->class->newInstanceWithoutConstructor();
            $this->observer->hydrating($object);
            foreach ($data as $attribute => $value) {
                $this->setter->call($object, $attribute, $value);
            }
            return $object;
        } catch (Throwable $exception) {
            throw HydrationFailed::encountered($exception, $this->class);
        }
    }
}
