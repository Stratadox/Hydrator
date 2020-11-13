<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydrator\HydrationFailure;
use Stratadox\Hydrator\Hydrator;
use Stratadox\Hydrator\MappedHydrator;
use Stratadox\Hydrator\Test\Data\ExceptionMessages;
use Stratadox\Hydrator\Test\Data\MappedHydratorsWithHydrationData;
use Stratadox\Hydrator\Test\Fixture\Popo;
use Stratadox\Hydrator\Test\Fixture\Throwing;

class Mapping_transforms_the_input_for_hydration extends TestCase
{
    use ExceptionMessages, MappedHydratorsWithHydrationData;

    /**
     * @test
     * @dataProvider mappedHydratorWithHydrationData
     */
    function mapping_the_input_data_before_hydrating(
        Hydrator $hydrator,
        array $propertyMappings,
        array $inputData,
        array $expectedProperties
    ) {
        $object = new Popo;
        $mappedHydrator = MappedHydrator::using($hydrator, ...$propertyMappings);

        $mappedHydrator->writeTo($object, $inputData);

        foreach ($expectedProperties as $name => $value) {
            self::assertEquals($value, $object->$name);
        }
    }

    /**
     * @test
     * @dataProvider exceptionMessages
     */
    function throwing_the_right_exception(Hydrator $hydrator, string $message)
    {
        $mappedHydrator = MappedHydrator::using($hydrator, Throwing::withMessage($message));

        $popo = Popo::class;
        $this->expectException(HydrationFailure::class);
        $this->expectExceptionMessage("Could not hydrate the `$popo`: $message");

        $mappedHydrator->writeTo(new Popo, ['foo' => 'bar']);
    }
}
