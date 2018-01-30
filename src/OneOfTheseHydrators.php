<?php

declare(strict_types = 1);

namespace Stratadox\Hydration\Hydrator;

use Stratadox\Hydrator\Hydrates;
use Stratadox\Hydration\UnmappableInput;

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
    private $current;

    private function __construct(string $decisionKey, array $hydratorMap)
    {
        $this->decisionKey = $decisionKey;
        $this->hydratorMap = $hydratorMap;
        foreach ($hydratorMap as $key => $instance) {
            $this->mustBeAString($key);
            $this->mustBeAHydrator($instance);
        }
    }

    public static function decideBasedOnThe(string $key, array $map) : Hydrates
    {
        return new OneOfTheseHydrators($key, $map);
    }

    /**
     * @throws UnmappableInput
     * @inheritdoc
     */
    public function fromArray(array $input)
    {
        try {
            $this->current = $this->hydratorBasedOn($this->keyFromThe($input));
            return $this->hydrateAnInstanceUsing($this->current, $input);
        } finally {
            $this->current = null;
        }
    }

    public function currentInstance()
    {
        if (!$this->current instanceof Hydrates) {
            return null;
        }
        return $this->current->currentInstance();
    }

    private function hydrateAnInstanceUsing(Hydrates $hydrator, array $input)
    {
        return $hydrator->fromArray($input);
    }

    /** @throws UnmappableInput */
    private function hydratorBasedOn(string $key) : Hydrates
    {
        if (!isset($this->hydratorMap[$key])) {
            throw CannotDecideOnAHydrator::withThis($key);
        }
        return $this->hydratorMap[$key];
    }

    /** @throws UnmappableInput */
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
