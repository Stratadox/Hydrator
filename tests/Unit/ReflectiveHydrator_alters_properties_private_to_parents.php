<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Unit;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use Stratadox\Hydrator\HydrationFailure;
use Stratadox\Hydrator\ReflectiveHydrator;
use Stratadox\Hydrator\Test\Data\ObjectsWithPropertiesTheyCannotAccess;
use Stratadox\Hydrator\Test\Data\TwentyFiveRandomSamples;
use Stratadox\Hydrator\Test\Fixture\ChildWithoutPropertyAccess;
use Stratadox\Hydrator\Test\Fixture\NoMagic;
use Stratadox\Hydrator\Test\Fixture\Popo;

class ReflectiveHydrator_alters_properties_private_to_parents extends TestCase
{
    use ObjectsWithPropertiesTheyCannotAccess, TwentyFiveRandomSamples;

    /**
     * @test
     * @dataProvider objectsWithPropertiesTheyCannotAccess
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

        self::assertTrue($actual->equals($expected));
        self::assertFalse($actual->equals($unexpected));
    }

    /** @test */
    function writing_properties_as_public_when_they_are_not_defined()
    {
        $object = new Popo;
        ReflectiveHydrator::default()->writeTo($object, ['foo' => 'bar']);

        self::assertEquals('bar', $object->foo ?? '');
        self::assertTrue((new ReflectionProperty($object, 'foo'))->isPublic());
    }

    /** @test */
    function throwing_custom_exceptions()
    {
        $object = new NoMagic;
        $hydrator = ReflectiveHydrator::default();

        $noMagic = NoMagic::class;
        $this->expectException(HydrationFailure::class);
        $this->expectExceptionMessage(
            "Could not hydrate the `$noMagic`: Thou shalt not write to foo."
        );

        $hydrator->writeTo($object, ['foo' => 'bar']);
    }
}
