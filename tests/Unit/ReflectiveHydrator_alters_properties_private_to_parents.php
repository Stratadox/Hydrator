<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Unit;

use Faker\Factory;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Stratadox\Hydrator\ReflectiveHydrator;
use Stratadox\Hydrator\Test\Fixture\ChildWithoutPropertyAccess;

/**
 * @covers \Stratadox\Hydrator\ReflectiveHydrator
 */
class ReflectiveHydrator_alters_properties_private_to_parents extends TestCase
{
    private const TESTS = 25;

    /**
     * @test
     * @dataProvider privatePropertyInheritance
     */
    function hydrating_the_private_property_of_the_parent_through_reflection(
        string $value,
        ChildWithoutPropertyAccess $expected,
        ChildWithoutPropertyAccess $unexpected
    ) {
        /** @var ChildWithoutPropertyAccess $actual */
        $actual = (new ReflectionClass(ChildWithoutPropertyAccess::class))
            ->newInstanceWithoutConstructor();

        ReflectiveHydrator::default()->writeTo($actual, ['property' => $value]);

        $this->assertTrue($actual->equals($expected));
        $this->assertFalse($actual->equals($unexpected));
    }

    public function privatePropertyInheritance(): array
    {
        $sets = [];
        for ($i = self::TESTS; $i > 0; --$i) {
            $value = $this->randomString();
            $other = $this->randomString();
            $sets["$value / $other"] = [
                $value,
                ChildWithoutPropertyAccess::onlyWriteAtConstruction($value),
                ChildWithoutPropertyAccess::onlyWriteAtConstruction($other)
            ];
        }
        return $sets;
    }

    private function randomString(): string
    {
        $random = Factory::create();
        return $random->randomElement([
            $random->word,
            $random->sentence,
            $random->email,
            $random->name
        ]);
    }
}
