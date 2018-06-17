<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

/**
 * Base class for decorating hydrators with observable functionality.
 *
 * @author Stratadox
 */
abstract class Observe implements Hydrates
{
    private $hydrator;
    private $observer;

    private function __construct(
        Hydrates $hydrator,
        ObservesHydration $observer
    ) {
        $this->hydrator = $hydrator;
        $this->observer = $observer;
    }

    /**
     * Attaches an observer to a hydrator.
     *
     * @param Hydrates          $hydrator The hydrator to observe.
     * @param ObservesHydration $observer The observer to attach.
     * @return Hydrates                   The decorated hydrator.
     */
    public static function hydrating(
        Hydrates $hydrator,
        ObservesHydration $observer
    ): Hydrates {
        return new static($hydrator, $observer);
    }

    /** @throws CannotHydrate */
    final protected function hydrate(object $target, array $input): void
    {
        $this->hydrator->writeTo($target, $input);
    }

    final protected function observe(object $target, array $input): void
    {
        $this->observer->hydrating($target, $input);
    }
}
