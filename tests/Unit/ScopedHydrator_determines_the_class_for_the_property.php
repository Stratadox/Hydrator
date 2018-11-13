<?php declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Stratadox\Hydrator\CannotHydrate;
use Stratadox\Hydrator\ScopedHydrator;
use Stratadox\Hydrator\Test\Data\ObjectsWithPropertiesTheyCannotAccess;
use Stratadox\Hydrator\Test\Data\TwentyFiveRandomSamples;
use Stratadox\Hydrator\Test\Fixture\ChildWithPrivateProperty;
use Stratadox\Hydrator\Test\Fixture\GrandChildWithPrivateProperty;

/**
 * @covers \Stratadox\Hydrator\ScopedHydrator
 */
class ScopedHydrator_determines_the_class_for_the_property extends TestCase
{
    use ObjectsWithPropertiesTheyCannotAccess, TwentyFiveRandomSamples;

    /**
     * @test
     * @dataProvider objectsWithPropertiesThatAlsoExistInTheParent
     */
    function hydrating_the_private_property_of_the_given_scope(
        string $expectation,
        string $myValue,
        string $parentValue
    ) {
        /** @var ChildWithPrivateProperty $object */
        $object = (new ReflectionClass(ChildWithPrivateProperty::class))
            ->newInstanceWithoutConstructor();

        ScopedHydrator::default()->writeTo($object, [
            'property' => $myValue,
            'parent.property' => $parentValue,
        ]);

        $this->assertSame($expectation, $object->property());
    }

    /**
     * @test
     * @dataProvider objectsWithPropertiesThatAlsoExistInBothParents
     */
    function hydrating_the_same_private_property_of_multiple_parents(
        string $expectation,
        string $myValue,
        string $parentValue,
        string $grandParentValue
    ) {
        /** @var GrandChildWithPrivateProperty $object */
        $object = (new ReflectionClass(GrandChildWithPrivateProperty::class))
            ->newInstanceWithoutConstructor();

        ScopedHydrator::default()->writeTo($object, [
            'property' => $myValue,
            'parent.property' => $parentValue,
            'parent.parent.property' => $grandParentValue,
        ]);

        $this->assertSame($expectation, $object->property());
    }

    /**
     * @test
     * @dataProvider objectsWithPropertiesThatAlsoExistInBothParents
     */
    function using_a_custom_scope_prefix(
        string $expectation,
        string $myValue,
        string $parentValue,
        string $grandParentValue
    ) {
        /** @var GrandChildWithPrivateProperty $object */
        $object = (new ReflectionClass(GrandChildWithPrivateProperty::class))
            ->newInstanceWithoutConstructor();

        ScopedHydrator::prefixedWith('.')->writeTo($object, [
            'property' => $myValue,
            '.property' => $parentValue,
            '..property' => $grandParentValue,
        ]);

        $this->assertSame($expectation, $object->property());
    }

    /** @test */
    function not_allowing_empty_prefixes()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The prefix for the scoped hydrator cannot be empty.'
        );

        ScopedHydrator::prefixedWith('');
    }

    /** @test */
    function throwing_an_exception_when_the_scope_is_not_resolvable()
    {
        /** @var ChildWithPrivateProperty $object */
        $object = (new ReflectionClass(ChildWithPrivateProperty::class))
            ->newInstanceWithoutConstructor();

        $class = ChildWithPrivateProperty::class;
        $this->expectException(CannotHydrate::class);
        $this->expectExceptionMessage(
            "Could not hydrate the `$class`: It has no parent.parent.property."
        );
        ScopedHydrator::default()->writeTo($object, [
            'parent.parent.property' => 'foo',
        ]);
    }
}
