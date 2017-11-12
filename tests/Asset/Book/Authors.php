<?php

namespace Stratadox\Hydration\Test\Asset\Book;

use Stratadox\ImmutableCollection\ImmutableCollection;

class Authors extends ImmutableCollection
{
    public function __construct(Author ...$authors)
    {
        parent::__construct(...$authors);
    }
}
