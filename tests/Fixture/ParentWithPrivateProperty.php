<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Fixture;

abstract class ParentWithPrivateProperty
{
    private $property;

    protected function __construct(string $property)
    {
        $this->property = $property;
    }

    public function property(): string
    {
        return $this->property;
    }
}
