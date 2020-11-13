<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

use Stratadox\HydrationMapping\Mapping;
use Stratadox\HydrationMapping\MappingFailure;

/**
 * Applies hydration mapping to the input data before hydrating.
 *
 * @author Stratadox
 */
final class MappedHydrator implements Hydrator
{
    /** @var Hydrator */
    private $hydrator;
    /** @var Mapping[] */
    private $properties;

    private function __construct(
        Hydrator $hydrator,
        Mapping ...$properties
    ) {
        $this->hydrator = $hydrator;
        $this->properties = $properties;
    }

    /**
     * Enables hydration mapping for a hydrator.
     *
     * @param Hydrator       $hydrator   The hydrator to decorate with mapping.
     * @param Mapping     ...$properties The mapping to apply to the input data.
     * @return Hydrator                  A decorated hydrator that maps the data.
     */
    public static function using(
        Hydrator $hydrator,
        Mapping ...$properties
    ): Hydrator {
        return new MappedHydrator($hydrator, ...$properties);
    }

    /** @inheritdoc */
    public function writeTo(object $target, array $input): void
    {
        $data = [];
        try {
            foreach ($this->properties as $property) {
                $data[$property->name()] = $property->value($input, $target);
            }
        } catch (MappingFailure $exception) {
            throw HydrationFailed::encountered($exception, $target, $input);
        }
        $this->hydrator->writeTo($target, $data);
    }
}
