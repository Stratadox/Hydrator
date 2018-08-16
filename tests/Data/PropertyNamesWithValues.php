<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Data;

use Faker\Factory;

trait PropertyNamesWithValues
{
    public function propertyNamesWithValues(): array
    {
        $random = Factory::create()->unique();
        $sets = [];
        for ($i = self::numberOfTests(); $i > 0; --$i) {
            $name = $random->word;
            $value = $random->randomElement([
                $random->word,
                $random->sentence,
                $random->email,
                $random->name
            ]);
            $sets["$name => $value"] = [$name, $value];
        }
        return $sets;
    }

    abstract protected static function numberOfTests(): int;
}
