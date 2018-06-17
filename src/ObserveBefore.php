<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

final class ObserveBefore implements Hydrates
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
        return new ObserveBefore($hydrator, $observer);
    }

    public function writeTo(object $target, array $input): void
    {
        $this->observer->hydrating($target, $input);
        $this->hydrator->writeTo($target, $input);
    }
}
