<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator;

/**
 * Delegates hydration, selecting a hydrator based on an input value.
 *
 * @package Stratadox\Hydrate
 * @author Stratadox
 */
final class OneOfTheseHydrators implements Hydrates
{
    private $decisionKey;
    private $hydratorMap;

    private function __construct(string $decisionKey, array $hydratorMap)
    {
        $this->decisionKey = $decisionKey;
        $this->hydratorMap = $hydratorMap;
        foreach ($hydratorMap as $key => $instance) {
            $this->mustBeAString($key);
            $this->mustBeAHydrator($instance);
        }
    }

    public static function decideBasedOnThe(string $key, array $map) : self
    {
        return new OneOfTheseHydrators($key, $map);
    }

    /** @inheritdoc */
    public function fromArray(array $input)
    {
        return $this->hydrateAnInstanceUsing(
            $this->hydratorBasedOn($this->keyFromThe($input)),
            $input
        );
    }

    private function hydrateAnInstanceUsing(Hydrates $hydrator, array $input)
    {
        return $hydrator->fromArray($input);
    }

    /** @throws CouldNotHydrate */
    private function hydratorBasedOn(string $key) : Hydrates
    {
        if (!isset($this->hydratorMap[$key])) {
            throw CannotDecideOnAHydrator::withThis($key);
        }
        return $this->hydratorMap[$key];
    }

    /** @throws CouldNotHydrate */
    private function keyFromThe(array $input) : string
    {
        if (!isset($input[$this->decisionKey])) {
            throw CannotDecideOnAHydrator::without($this->decisionKey);
        }
        return $input[$this->decisionKey];
    }

    private function mustBeAString(string $key) : void {}
    private function mustBeAHydrator(Hydrates $instance) : void {}
}
