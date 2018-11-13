<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Fixture;

/**
 * Child with private property.
 *
 * This class has a private property named "property", which is a scenario
 * worthy of testing because its parent class also has a private property with
 * that same name.
 *
 * @author Stratadox
 */
class ChildWithPrivateProperty extends ParentWithPrivateProperty
{
    private $property;

    protected function __construct(string $myProperty, string $parentProperty)
    {
        $this->property = $myProperty;
        parent::__construct($parentProperty);
    }

    public static function thatIsNamedAfterItsParent(
        string $myProperty,
        string $parentProperty
    ): self {
        return new self($myProperty, $parentProperty);
    }

    public function property(): string
    {
        return 'Child: ' . $this->property . '; Parent: ' . parent::property();
    }
}
