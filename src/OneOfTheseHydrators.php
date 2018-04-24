<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

/**
 * Delegates hydration, selecting a hydrator based on an input value.
 *
 * @package Stratadox\Hydrate
 * @author  Stratadox
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

    /**
     * Creates a new selective hydrator.
     *
     * @param string $key The key in which the decision key is stored.
     * @param array  $map Map of hydrators as [string $key => Hydrates $hydrator].
     * @return Hydrates   The delegating hydrator.
     */
    public static function decideBasedOnThe(string $key, array $map): Hydrates
    {
        return new self($key, $map);
    }

    /** @inheritdoc */
    public function fromArray(array $input)
    {
        return $this->hydrateAnInstanceUsing(
            $this->hydratorBasedOn($this->keyFromThe($input)),
            $input
        );
    }

    /** @inheritdoc */
    public function classFor(array $input): string
    {
        return $this->decideOnAClassUsing(
            $this->hydratorBasedOn($this->keyFromThe($input)),
            $input
        );
    }

    /**
     * Delegate the hydration to the given hydrator.
     *
     * @param Hydrates $hydrator The hydrator to delegate to.
     * @param array    $input    The input data.
     * @return mixed|object      The hydrated instance.
     * @throws CannotHydrate     When the input data could not be hydrated.
     */
    private function hydrateAnInstanceUsing(Hydrates $hydrator, array $input)
    {
        return $hydrator->fromArray($input);
    }

    /**
     * Ask the hydrator for this input which class it would hydrate.
     *
     * @param Hydrates $hydrator The hydrator to delegate to.
     * @param array    $input    The input data.
     * @return string            The class for this data.
     * @throws CannotHydrate     In very unlikely cases.
     */
    private function decideOnAClassUsing(Hydrates $hydrates, array $input): string
    {
        return $hydrates->classFor($input);
    }

    /**
     * Selects a hydrator based on a key.
     *
     * @param string $key    The key to use for hydrator selection.
     * @return Hydrates      The hydrator to use.
     * @throws CannotHydrate When the selection key is invalid.
     */
    private function hydratorBasedOn(string $key): Hydrates
    {
        if (!isset($this->hydratorMap[$key])) {
            throw CannotDecideOnAHydrator::withThis($key);
        }
        return $this->hydratorMap[$key];
    }

    /**
     * Retrieves the key from the input data.
     *
     * @param array $input   The input data.
     * @return string        The key to use in selecting a hydrator.
     * @throws CannotHydrate When the key is not present in the input data.
     */
    private function keyFromThe(array $input): string
    {
        if (!isset($input[$this->decisionKey])) {
            throw CannotDecideOnAHydrator::without($this->decisionKey);
        }
        return $input[$this->decisionKey];
    }

    private function mustBeAString(string $key): void
    {
    }

    private function mustBeAHydrator(Hydrates $instance): void
    {
    }
}
