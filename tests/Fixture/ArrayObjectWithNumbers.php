<?php declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Fixture;

use ArrayIterator;
use ArrayObject;
use InvalidArgumentException;
use function is_numeric;

final class ArrayObjectWithNumbers extends ArrayObject
{
    public function __construct(
        $input = [],
        int $flags = 0,
        string $iterator_class = ArrayIterator::class
    ) {
        foreach ($input as $value) {
            $this->mustBeNumeric($value);
        }
        parent::__construct($input, $flags, $iterator_class);
    }

    public static function empty(): self
    {
        return new self();
    }

    public function offsetSet($index, $value)
    {
        $this->mustBeNumeric($value);
        parent::offsetSet($index, $value);
    }

    private function mustBeNumeric($value): void
    {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException('Input must be numeric.');
        }
    }
}
