<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Asset\Numbers;

use ArrayAccess;
use ArrayIterator;
use BadMethodCallException;
use function count;
use Countable;
use IteratorAggregate;
use Traversable;

class CollectionWithPrivateConstructor implements Countable, IteratorAggregate, ArrayAccess
{
    private $numbers;

    private function __construct(int ...$numbers)
    {
        $this->numbers = $numbers;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->numbers);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->numbers[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->numbers[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException('Not allowed');
    }

    public function offsetUnset($offset)
    {
        throw new BadMethodCallException('Not allowed');
    }

    public function count()
    {
        return count($this->numbers);
    }
}
