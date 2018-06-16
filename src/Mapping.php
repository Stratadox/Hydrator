<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

use Stratadox\HydrationMapping\MapsProperties;
use Stratadox\HydrationMapping\UnmappableInput;

final class Mapping implements Hydrates
{
    private $hydrator;
    private $properties;

    public function __construct(
        Hydrates $hydrator,
        MapsProperties $properties
    ) {
        $this->hydrator = $hydrator;
        $this->properties = $properties;
    }

    public static function for(
        Hydrates $hydrator,
        MapsProperties $properties
    ): Hydrates {
        return new Mapping($hydrator, $properties);
    }

    /** @inheritdoc */
    public function writeTo(object $target, array $input): void
    {
        $data = [];
        try {
            foreach ($this->properties as $property) {
                $data[$property->name()] = $property->value($input, $target);
            }
        } catch (UnmappableInput $exception) {
            throw HydrationFailed::encountered($exception, $target);
        }
        $this->hydrator->writeTo($target, $data);
    }
}
