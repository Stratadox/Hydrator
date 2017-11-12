<?php

namespace Stratadox\Hydration\Test\Asset\FooBar;

class Foo
{
    private $baz;

    private function __construct() {}
    private function __clone() {}

    public function baz() : string
    {
        return $this->baz;
    }
}
