<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Data;

use Faker\Factory;
use Stratadox\Hydrator\Test\Fixture\ChildWithoutPropertyAccess;
use Stratadox\Hydrator\Test\Fixture\ChildWithPrivateProperty;

trait ObjectsWithPropertiesTheyCannotAccess
{
    private $randomPropertyValue;

    public function objectsWithPropertiesTheyCannotAccess(): iterable
    {
        for ($i = self::numberOfTests(); $i > 0; --$i) {
            $value = $this->randomString();
            $other = $this->randomString();
            yield "$value / $other" => [
                $value,
                ChildWithoutPropertyAccess::onlyWriteAtConstruction($value),
                ChildWithoutPropertyAccess::onlyWriteAtConstruction($other),
            ];
        }
    }

    public function objectsWithPropertiesThatAlsoExistInTheParent(): iterable
    {
        for ($i = self::numberOfTests(); $i > 0; --$i) {
            $myValue = $this->randomString();
            $parentValue = $this->randomString();
            $expectation = "Child: $myValue; Parent: $parentValue";
            yield $expectation => [
                $expectation,
                $myValue,
                $parentValue,
            ];
        }
    }

    public function objectsWithPropertiesThatAlsoExistInBothParents(): iterable
    {
        for ($i = self::numberOfTests(); $i > 0; --$i) {
            $myValue = $this->randomString();
            $parentValue = $this->randomString();
            $grandParentValue = $this->randomString();
            $expectation =
                "Grandchild: $myValue; " .
                "Child: $parentValue; " .
                "Parent: $grandParentValue";
            yield $expectation => [
                $expectation,
                $myValue,
                $parentValue,
                $grandParentValue,
            ];
        }
    }

    private function randomString(): string
    {
        if (null === $this->randomPropertyValue) {
            $this->randomPropertyValue = Factory::create()->unique();
        }
        return $this->randomPropertyValue->randomElement([
            $this->randomPropertyValue->word,
            $this->randomPropertyValue->sentence,
            $this->randomPropertyValue->email,
            $this->randomPropertyValue->name
        ]);
    }

    abstract protected static function numberOfTests(): int;
}
