<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Data;

use function array_map;
use function implode;
use function random_int;
use function range;

trait RandomIntegers
{
    public function integers(): array
    {
        $sets = [];
        for ($i = self::numberOfTests(); $i > 0; --$i) {
            $integers = array_map(function (): int {
                return random_int(-1000, 1000);
            }, range(0, random_int(1, 10)));
            $sets[implode(',', $integers)] = $integers;
        }
        return $sets;
    }

    abstract protected static function numberOfTests(): int;
}
