<?php

declare(strict_types = 1);

namespace Stratadox\Hydration\Test\Asset\Book;

class Image implements Element
{
    private $src;

    public function __construct(string $src)
    {
        $this->src = $src;
    }

    public function __toString() : string
    {
        return $this->src;
    }
}
