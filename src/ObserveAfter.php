<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

final class ObserveAfter implements Hydrates
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

    public static function hydrating(
        Hydrates $hydrator,
        ObservesHydration $observer
    ): Hydrates {
        return new ObserveAfter($hydrator, $observer);
    }

    public function writeTo(object $target, array $input): void
    {
        $this->hydrator->writeTo($target, $input);
        $this->observer->hydrating($target, $input);
    }
}
