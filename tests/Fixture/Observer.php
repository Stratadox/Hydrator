<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Fixture;

use Stratadox\Hydrator\HydrationObserver;

final class Observer implements HydrationObserver
{
    private $instances = [];
    private $dataSets = [];

    public function hydrating(object $theInstance, array $theData): void
    {
        $this->instances[] = $theInstance;
        $this->dataSets[] = $theData;
    }

    public function observedInstances(): array
    {
        return $this->instances;
    }

    public function observedInstance(int $i = 0): object
    {
        return $this->instances[$i];
    }

    public function observedDataSets(): array
    {
        return $this->dataSets;
    }

    public function observedDataSet(int $i = 0): array
    {
        return $this->dataSets[$i];
    }
}
