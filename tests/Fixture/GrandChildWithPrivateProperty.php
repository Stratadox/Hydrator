<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Fixture;

/**
 * Child with private property.
 *
 * This class has a private property named "property", which is a scenario
 * worthy of testing because its parent class as well as its grandparent class
 * also has a private property with that same name.
 *
 * @author Stratadox
 */
class GrandChildWithPrivateProperty extends ChildWithPrivateProperty
{
    private $property;

    protected function __construct(
        string $myProperty,
        string $parentProperty,
        string $grandParentProperty
    ) {
        $this->property = $myProperty;
        parent::__construct($parentProperty, $grandParentProperty);
    }

    public static function thatIsNamedAfterItsParents(
        string $myProperty,
        string $parentProperty,
        string $grandParentProperty
    ): self {
        return new self($myProperty, $parentProperty, $grandParentProperty);
    }

    public function property(): string
    {
        return 'Grandchild: ' . $this->property . '; ' . parent::property();
    }
}
