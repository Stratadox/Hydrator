<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Data;

use Faker\Factory;
use Stratadox\Hydrator\Test\Fixture\ChildWithoutPropertyAccess;

trait ObjectsWithPropertiesTheyCannotAccess
{
    public function objectsWithPropertiesTheyCannotAccess(): array
    {
        $sets = [];
        for ($i = self::numberOfTests(); $i > 0; --$i) {
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

    abstract protected static function numberOfTests(): int;
}
