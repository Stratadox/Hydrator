<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator;

use Throwable;
use ReflectionClass as Reflected;

/**
 * Hydrates an object by calling its constructor with squashed array input.
 *
 * @package Stratadox\Hydrate
 * @author Stratadox
 */
final class VariadicConstructor implements Hydrates
{
    private $class;

    private function __construct(string $forTheClass)
    {
        $this->class = $forTheClass;
    }

    /**
     * Creates a new variadic constructor calling hydrator.
     *
     * @param string $class The class to hydrate.
     * @return self         The variadic construction calling hydrator.
     */
    public static function forThe(string $class) : self
    {
        return new self($class);
    }

    /** @inheritdoc */
    public function fromArray(array $input)
    {
        $class = $this->class;
        try {
            return new $class(...$input);
        } catch (Throwable $exception) {
            throw HydrationFailed::encountered($exception, new Reflected($class));
        }
    }
}
