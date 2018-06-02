<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

use ReflectionException;
use ReflectionObject as Reflected;
use Stratadox\Instantiator\CannotInstantiateThis;
use Stratadox\Instantiator\Instantiator;
use Stratadox\Instantiator\ProvidesInstances;
use Throwable;

/**
 * Hydrates an object by calling its constructor with squashed array input.
 *
 * @package Stratadox\Hydrate
 * @author  Stratadox
 */
final class VariadicConstructor implements Hydrates
{
    private $make;

    private function __construct(ProvidesInstances $forTheClass)
    {
        $this->make = $forTheClass;
    }

    /**
     * Creates a new variadic constructor calling hydrator.
     *
     * @param string $class          The class to hydrate.
     * @return Hydrates              The variadic construction calling hydrator.
     * @throws CannotInstantiateThis When the class is not instantiable.
     */
    public static function forThe(string $class): Hydrates
    {
        return new self(Instantiator::forThe($class));
    }

    /** @inheritdoc */
    public function fromArray(array $input)
    {
        try {
            return $this->newObjectFrom($input);
        } catch (Throwable $exception) {
            throw HydrationFailed::encountered($exception, $this->make->class());
        }
    }

    /** @throws CannotInstantiateThis|ReflectionException */
    private function newObjectFrom(array $input)
    {
        $object = $this->make->instance();
        $constructor = (new Reflected($object))->getMethod('__construct');
        $constructor->setAccessible(true);
        $constructor->invoke($object, ...$input);
        return $object;
    }

    /** @inheritdoc */
    public function classFor(array $input): string
    {
        return $this->make->class();
    }
}
