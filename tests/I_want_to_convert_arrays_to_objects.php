<?php

namespace Stratadox\Hydrator\Test;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydration\Hydrates;
use Stratadox\Hydration\Hydrator\SimpleHydrator;
use Stratadox\Hydration\Test\Asset\Book\Title;
use Stratadox\Hydration\Test\Asset\FooBar\Bar;
use Stratadox\Hydration\Test\Asset\FooBar\Foo;
use Stratadox\Hydration\Test\Asset\FooBar\FooCollection;

class I_want_to_convert_arrays_to_objects extends TestCase
{
    use FooBarAssertions;

    /** @var Hydrates */
    private $makeNewFoo;

    /** @var Hydrates */
    private $makeNewBar;

    /** @var Hydrates */
    private $makeNewTitle;

    /**
     * Checks that the [@see SimpleHydrator] can create an instance of the
     * [@see Foo] class.
     *
     * The Foo class represents a simple value object with a simple string value.
     * Its constructor is private, it is not cloneable and has no setters...
     * Piece of cake for our [@see SimpleHydrator]!
     *
     * @scenario
     */
    function making_a_Foo()
    {
        $foo = $this->makeNewFoo->fromArray([
            'baz' => 'BAZ?'
        ]);

        $this->assertInstanceOf(Foo::class, $foo,
            'The hydrator should produce an instance of the Foo class.'
        );

        $this->assertSame('BAZ?', $foo->baz(),
            'The foo object should contain the data as given to the hydrator.'
        );
    }

    /**
     * Checks that the [@see SimpleHydrator] can create an instance of the
     * [@see Bar] class.
     *
     * The [@see Bar] class represents a value object that holds references to at
     * least one [@see Foo] object. That makes it a challenge for our
     * SimpleHydrator, but its nothing we can't handle.
     *
     * Converting an array to a Bar object without any mapping is a slightly
     * heavier task than what the SimpleHydrator was designed for. It requires
     * more userland code than [@see I_want_to_map_nested_arrays_to_objects] or
     * [@see I_want_to_map_plain_arrays_to_lazy_entities] and doesn't load lazily.
     *
     * Nonetheless, as we see in this test case, it is possible to hydrate an
     * object that has relationships to other to-be-hydrated objects using only
     * the SimpleHydrator (albeit two of them)
     *
     * @scenario
     */
    function making_a_Bar()
    {
        $bar = $this->makeNewBar->fromArray([
            'foo' => $this->makeNewFoo->fromArray(['baz' => 'Baz 1']),
            'foos' => new FooCollection(
                $this->makeNewFoo->fromArray(['baz' => 'Baz 2']),
                $this->makeNewFoo->fromArray(['baz' => 'Baz 3'])
            )
        ]);

        $this->assertBaz('Baz 1', $bar->foo(),
            'The bar object should contain a foo object with the right baz value.'
        );

        $this->assertBaz('Baz 2', $bar->foos()[0],
            'The bar object should contain foo objects with the right baz value.'
        );

        $this->assertBaz('Baz 3', $bar->foos()[1],
            'The bar object should contain foo objects with the right baz value.'
        );
    }

    /**
     * Sets up the hydrators.
     *
     * These are [@see SimpleHydrator]s, one for each of the test assets that
     * are being hydrated in the above scenarios.
     */
    protected function setUp()
    {
        $this->makeNewFoo = SimpleHydrator::forThe(Foo::class);
        $this->makeNewBar = SimpleHydrator::forThe(Bar::class);
        $this->makeNewTitle = SimpleHydrator::forThe(Title::class);
    }
}
