<?php

declare(strict_types=1);

namespace Stratadox\Hydrator\Test;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;
use Stratadox\Hydrator\Test\Asset\Book\Title;
use Stratadox\Hydrator\Test\Asset\FooBar\Bar;
use Stratadox\Hydrator\Test\Asset\FooBar\Foo;
use Stratadox\Hydrator\Test\Asset\FooBar\FooCollection;
use Stratadox\Hydrator\CouldNotHydrate;
use Stratadox\Hydrator\Hydrates;
use Stratadox\Hydrator\ObservesHydration;
use Stratadox\Hydrator\SimpleHydrator;
use Stratadox\Instantiator\ProvidesInstances;

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

    /** @test */
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

    /** @test */
    function making_a_Bar()
    {
        $bar = $this->makeNewBar->fromArray([
            'foo'  => $this->makeNewFoo->fromArray(['baz' => 'Baz 1']),
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
    function using_a_custom_instantiator()
    {
        $object1 = new stdClass();
        $object2 = new stdClass();

        /** @var ProvidesInstances|MockObject $instantiator */
        $instantiator = $this->createMock(ProvidesInstances::class);
        $instantiator->expects($this->exactly(2))
            ->method('instance')
            ->willReturnOnConsecutiveCalls($object1, $object2);

        $hydrator = SimpleHydrator::withInstantiator(
            $instantiator
        );

        $result1 = $hydrator->fromArray([]);
        $result2 = $hydrator->fromArray([]);

        $this->assertSame($object1, $result1);
        $this->assertSame($object2, $result2);
    }

    /** @test */
    function throwing_the_expected_exception_when_things_go_wrong()
    {
        $hydrator = SimpleHydrator::forThe(
            '\\stdClass',
            null
        );

        $this->expectException(CouldNotHydrate::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Could not load the class `stdClass`: Cannot bind closure to scope of internal class stdClass'
        );
        $hydrator->fromArray(['foo' => 'bar']);
    }

    public function hydrator(): Hydrates
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
