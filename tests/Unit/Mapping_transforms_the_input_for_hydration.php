<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Unit;

use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;
use Stratadox\HydrationMapping\MapsProperties;
use Stratadox\Hydrator\Hydrates;
use Stratadox\Hydrator\Mapping;
use Stratadox\Hydrator\ObjectHydrator;
use Stratadox\Hydrator\ReflectiveHydrator;
use Stratadox\Hydrator\Test\Fixture\Popo;
use Stratadox\Hydrator\Test\Fixture\Properties;
use Stratadox\Hydrator\Test\Fixture\Rename;

/**
 * @covers \Stratadox\Hydrator\Mapping
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
        $hydrator = Mapping::for($hydrator, $propertyMappings);

        $hydrator->writeTo($object, $inputData);

        foreach ($expectedProperties as $name => $value) {
            $this->assertAttributeEquals($value, $name, $object);
        }
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
