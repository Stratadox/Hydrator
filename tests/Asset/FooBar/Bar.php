<?php

namespace Stratadox\Hydration\Test\Asset\FooBar;

class Bar
{
    private $foo;
    private $foos;

    private function __construct() {}
    private function __clone() {}

    public function foos() : FooCollection
    {
        return $this->foos;
    }

    public function foo() : Foo
    {
        return $this->foo;
    }
}
