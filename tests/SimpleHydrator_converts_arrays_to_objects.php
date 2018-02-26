<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator\Test;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Stratadox\Hydrator\Test\Asset\Book\Title;
use Stratadox\Hydrator\Test\Asset\FooBar\Bar;
use Stratadox\Hydrator\Test\Asset\FooBar\Foo;
use Stratadox\Hydrator\Test\Asset\FooBar\FooCollection;
use Stratadox\Hydrator\CouldNotHydrate;
use Stratadox\Hydrator\Hydrates;
use Stratadox\Hydrator\ObservesHydration;
use Stratadox\Hydrator\SimpleHydrator;

/**
 * @covers \Stratadox\Hydrator\SimpleHydrator
 * @covers \Stratadox\Hydrator\HydrationFailed
 * @covers \Stratadox\Hydrator\BlindObserver
 */
class SimpleHydrator_converts_arrays_to_objects extends TestCase
{
    use FooBarAssertions;

    /** @var Hydrates */
    private $makeNewFoo;

    /** @var Hydrates */
    private $makeNewBar;

    /** @var Hydrates */
    private $hydrator;

    /**
     * Checks that the [@see SimpleHydrator] can create an instance of the
     * [@see Foo] class.
     *
     * The Foo class represents a simple value object with a simple string value.
     * Its constructor is private, it is not cloneable and has no setters...
     * Piece of cake for our [@see SimpleHydrator]!
     *
     * @test
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
     * @test
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

    /** @test */
    function notifying_the_observers()
    {
        $emptyObject = (new ReflectionClass(Title::class))->newInstanceWithoutConstructor();

        /** @var ObservesHydration|MockObject $observer */
        $observer = $this->createMock(ObservesHydration::class);
        $observer->expects($this->once())->method('hydrating')->with($emptyObject);

        $hydrator = SimpleHydrator::forThe(
            Title::class,
            null,
            $observer
        );

        $hydrator->fromArray(['foo' => 'bar']);
    }

    /** @test */
    function throwing_the_expected_exception_when_things_go_wrong()
    {
        $hydrator = SimpleHydrator::forThe(
            '\\stdClass',
            null
        );

        $this->expectException(CouldNotHydrate::class);
        $this->expectExceptionMessage(
            'Could not load the class `stdClass`: Cannot bind closure to scope of internal class stdClass'
        );
        $hydrator->fromArray(['foo' => 'bar']);
    }

    public function hydrator() : Hydrates
    {
        return $this->hydrator;
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
    }
}
