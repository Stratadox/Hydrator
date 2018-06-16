<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Unit;

use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;
use Stratadox\HydrationMapping\MapsProperties;
use Stratadox\Hydrator\CannotHydrate;
use Stratadox\Hydrator\Hydrates;
use Stratadox\Hydrator\Mapping;
use Stratadox\Hydrator\ObjectHydrator;
use Stratadox\Hydrator\ReflectiveHydrator;
use Stratadox\Hydrator\Test\Fixture\Popo;
use Stratadox\Hydrator\Test\Fixture\Properties;
use Stratadox\Hydrator\Test\Fixture\Rename;
use Stratadox\Hydrator\Test\Fixture\Throwing;

/**
 * @covers \Stratadox\Hydrator\Mapping
 * @covers \Stratadox\Hydrator\HydrationFailed
 */
class Mapping_transforms_the_input_for_hydration extends TestCase
{
    /**
     * @test
     * @dataProvider inputData
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

    public function inputData(): array
    {
        $random = Factory::create();
        $hydrators = [
            'closure binding hydrator' => ObjectHydrator::default(),
            'reflection property hydrator' => ReflectiveHydrator::default()
        ];
        $data = [];
        foreach ($hydrators as $name => $hydrator) {
            $data[$name . ' with camel case mapping'] = $this->camelCaseMapping($hydrator, $random);
            $data[$name . ' with prefix mapping'] = $this->prefixMapping($hydrator, $random);
        }
        return $data;
    }

    public function exceptionMessages(): array
    {
        $random = Factory::create();
        $sets = [];
        for ($i = 10; $i > 0; --$i) {
            $message = $random->sentence;
            $sets[$message] = [
                $random->randomElement([
                    ObjectHydrator::default(),
                    ReflectiveHydrator::default()
                ]),
                $message
            ];
        }
        return $sets;
    }

    private function camelCaseMapping(Hydrates $hydrator, Generator $random): array
    {
        $firstName = $random->firstName;
        $lastName = $random->lastName;
        return [
            $hydrator,
            Properties::use(
                Rename::between('first_name', 'firstName'),
                Rename::between('last_name', 'lastName')
            ),
            [
                'first_name' => $firstName,
                'last_name'  => $lastName,
            ],
            [
                'firstName' => $firstName,
                'lastName'  => $lastName,
            ]
        ];
    }

    private function prefixMapping(Hydrates $hydrator, Generator $random): array
    {
        $foo = $random->word;
        $bar = $random->sentence;
        return [
            $hydrator,
            Properties::use(
                Rename::between('foo', 'my_foo'),
                Rename::between('bar', 'my_bar')
            ),
            [
                'foo' => $foo,
                'bar'  => $bar,
            ],
            [
                'my_foo' => $foo,
                'my_bar'  => $bar,
            ]
        ];
    }
}
