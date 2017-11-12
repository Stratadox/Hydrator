<?php

namespace Stratadox\Hydration\Test\Asset;

use BadMethodCallException;
use SplFixedArray;

abstract class ReadOnlyCollection extends SplFixedArray
{
    public function __construct(...$values)
    {
        parent::__construct(count($values));

        foreach ($values as $position => $value) {
            parent::offsetSet($position, $value);
        }
    }

    final public static function fromArray($array, $save_indexes = null)
    {
        return new static(...$array);
    }

    final public function offsetSet($index, $value)
    {
        throw new BadMethodCallException;
    }

    final public function offsetUnset($index)
    {
        throw new BadMethodCallException;
    }
}
