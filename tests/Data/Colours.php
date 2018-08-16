<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Data;

use Faker\Factory;
use Stratadox\Hydrator\Test\Fixture\Colour;
use function substr;

trait Colours
{
    public function colours(): array
    {
        $random = Factory::create()->unique();
        $sets = [];
        for ($i = self::numberOfTests(); $i > 0; --$i) {
            $hex = $random->hexColor;
            $otherHex = $random->hexColor;
            $sets["$hex / $otherHex"] = [
                substr($hex, 1),
                Colour::withCode($hex),
                Colour::withCode($otherHex),
            ];
        }
        return $sets;
    }

    abstract protected static function numberOfTests(): int;
}
