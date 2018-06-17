<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

use Stratadox\HydrationMapping\MapsProperties;
use Stratadox\HydrationMapping\UnmappableInput;

/**
 * Applies hydration mapping to the input data before hydrating.
 *
 * @author Stratadox
 */
final class Mapping implements Hydrates
{
    private $hydrator;
    private $properties;

    private function __construct(
        Hydrates $hydrator,
        MapsProperties $properties
    ) {
        $this->hydrator = $hydrator;
        $this->properties = $properties;
    }

    /**
     * Enables hydration mapping for a hydrator.
     *
     * @param Hydrates       $hydrator   The hydrator to decorate with mapping.
     * @param MapsProperties $properties The mapping to apply to the input data.
     * @return Hydrates                  A decorated hydrator that maps the data.
     */
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
