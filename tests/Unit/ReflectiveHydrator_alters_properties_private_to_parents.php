<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Unit;

use Faker\Factory;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use Stratadox\Hydrator\CannotHydrate;
use Stratadox\Hydrator\ReflectiveHydrator;
use Stratadox\Hydrator\Test\Fixture\ChildWithoutPropertyAccess;
use Stratadox\Hydrator\Test\Fixture\NoMagic;
use Stratadox\Hydrator\Test\Fixture\Popo;

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

    /** @test */
    function writing_properties_as_public_when_they_are_not_defined()
    {
        $object = new Popo;
        ReflectiveHydrator::default()->writeTo($object, ['foo' => 'bar']);

        $this->assertAttributeEquals('bar', 'foo', $object);
        $this->assertTrue((new ReflectionProperty($object, 'foo'))->isPublic());
    }

    /** @test */
    function throwing_custom_exceptions()
    {
        $object = new NoMagic;
        $hydrator = ReflectiveHydrator::default();

        $noMagic = NoMagic::class;
        $this->expectException(CannotHydrate::class);
        $this->expectExceptionMessage(
            "Could not hydrate the `$noMagic`: Thou shalt not write to foo."
        );

        $hydrator->writeTo($object, ['foo' => 'bar']);
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
