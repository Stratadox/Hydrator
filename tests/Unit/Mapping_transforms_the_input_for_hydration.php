<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\HydrationMapping\MapsProperties;
use Stratadox\Hydrator\CannotHydrate;
use Stratadox\Hydrator\Hydrates;
use Stratadox\Hydrator\Mapping;
use Stratadox\Hydrator\Test\Data\ExceptionMessages;
use Stratadox\Hydrator\Test\Data\MappedHydratorsWithHydrationData;
use Stratadox\Hydrator\Test\Fixture\Popo;
use Stratadox\Hydrator\Test\Fixture\Properties;
use Stratadox\Hydrator\Test\Fixture\Throwing;

/**
 * @covers \Stratadox\Hydrator\Mapping
 * @covers \Stratadox\Hydrator\HydrationFailed
 */
class Mapping_transforms_the_input_for_hydration extends TestCase
{
    use ExceptionMessages, MappedHydratorsWithHydrationData;

    /**
     * @test
     * @dataProvider mappedHydratorWithHydrationData
     */
    function mapping_the_input_data_before_hydrating(
        Hydrates $hydrator,
        MapsProperties $propertyMappings,
        array $inputData,
        array $expectedProperties
    ) {
        $object = new Popo;
        $mappedHydrator = Mapping::for($hydrator, $propertyMappings);

        $mappedHydrator->writeTo($object, $inputData);

        foreach ($expectedProperties as $name => $value) {
            $this->assertAttributeEquals($value, $name, $object);
        }
    }

    /**
     * @test
     * @dataProvider exceptionMessages
     */
    function throwing_the_right_exception(Hydrates $hydrator, string $message)
    {
        $mappedHydrator = Mapping::for($hydrator, Properties::use(
            Throwing::withMessage($message)
        ));

        $popo = Popo::class;
        $this->expectException(CannotHydrate::class);
        $this->expectExceptionMessage("Could not hydrate the `$popo`: $message");

        $mappedHydrator->writeTo(new Popo, ['foo' => 'bar']);
    }
}
