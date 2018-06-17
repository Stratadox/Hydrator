<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Fixture;

use Stratadox\Hydrator\ObservesHydration;

final class Observer implements ObservesHydration
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

    public function observedInstance(int $i): object
    {
        return $this->instances[$i];
    }

    public function observedDataSets(): array
    {
        return $this->dataSets;
    }

    public function observedDataSet(int $i): array
    {
        return $this->dataSets[$i];
    }
}
