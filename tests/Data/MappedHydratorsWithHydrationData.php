<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Data;

use Faker\Factory;
use Faker\Generator;
use Stratadox\Hydrator\Hydrator;
use Stratadox\Hydrator\ObjectHydrator;
use Stratadox\Hydrator\ReflectiveHydrator;
use Stratadox\Hydrator\Test\Fixture\Rename;

trait MappedHydratorsWithHydrationData
{
    public function mappedHydratorWithHydrationData(): array
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


    private function camelCaseMapping(Hydrator $hydrator, Generator $random): array
    {
        $theirFirstName = $random->firstName;
        $theirLastName = $random->lastName;
        return [
            $hydrator,
            [
                Rename::between('first_name', 'firstName'),
                Rename::between('last_name', 'lastName')
            ],
            [
                'first_name' => $theirFirstName,
                'last_name'  => $theirLastName,
            ],
            [
                'firstName' => $theirFirstName,
                'lastName'  => $theirLastName,
            ]
        ];
    }

    private function prefixMapping(Hydrator $hydrator, Generator $random): array
    {
        $randomWordForFoo = $random->word;
        $randomSentenceForBar = $random->sentence;
        return [
            $hydrator,
            [
                Rename::between('foo', 'my_foo'),
                Rename::between('bar', 'my_bar')
            ],
            [
                'foo' => $randomWordForFoo,
                'bar'  => $randomSentenceForBar,
            ],
            [
                'my_foo' => $randomWordForFoo,
                'my_bar'  => $randomSentenceForBar,
            ]
        ];
    }
}
