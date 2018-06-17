<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Fixture;

use BadMethodCallException;

final class NoMagic
{
    public function __set(string $name, $value): void
    {
        throw new BadMethodCallException("Thou shalt not write to $name.");
    }

    public function __get(string $name)
    {
        throw new BadMethodCallException("Thou shalt not read the $name.");
    }

    public function __isset(string $name)
    {
        return false;
    }
}
