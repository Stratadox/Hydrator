<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

/**
 * Base class for decorating hydrators with observable functionality.
 *
 * @author Stratadox
 */
abstract class Observe implements Hydrator
{
    private $hydrator;
    private $observer;

    private function __construct(
        Hydrator $hydrator,
        HydrationObserver $observer
    ) {
        $this->hydrator = $hydrator;
        $this->observer = $observer;
    }

    /**
     * Attaches an observer to a hydrator.
     *
     * @param Hydrator          $hydrator The hydrator to observe.
     * @param HydrationObserver $observer The observer to attach.
     * @return Hydrator                   The decorated hydrator.
     */
    public static function hydrating(
        Hydrator $hydrator,
        HydrationObserver $observer
    ): Hydrator {
        return new static($hydrator, $observer);
    }

    /** @throws HydrationFailure */
    final protected function hydrate(object $target, array $input): void
    {
        $this->hydrator->writeTo($target, $input);
    }

    final protected function observe(object $target, array $input): void
    {
        $this->observer->hydrating($target, $input);
    }
}
