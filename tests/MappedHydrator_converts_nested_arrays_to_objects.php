<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator\Test;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Stratadox\Hydration\Test\Asset\Book\Author;
use Stratadox\Hydration\Test\Asset\Book\Book;
use Stratadox\Hydration\Test\Asset\Book\Contents;
use Stratadox\Hydration\Test\Asset\Book\Isbn;
use Stratadox\Hydration\Test\Asset\Book\Title;
use Stratadox\Hydration\Test\Asset\Properties;
use Stratadox\Hydration\Test\Asset\Unmappable;
use Stratadox\HydrationMapping\MapsProperties;
use Stratadox\HydrationMapping\MapsProperty;
use Stratadox\Hydrator\CouldNotHydrate;
use Stratadox\Hydrator\Hydrates;
use Stratadox\Hydrator\MappedHydrator;
use Stratadox\Hydrator\ObservesHydration;
use Stratadox\Hydrator\SimpleHydrator;
use Stratadox\Hydrator\VariadicConstructor;

/**
 * @covers \Stratadox\Hydrator\MappedHydrator
 * @covers \Stratadox\Hydrator\HydrationFailed
 * @covers \Stratadox\Hydrator\BlindObserver
 */
class MappedHydrator_converts_nested_arrays_to_objects extends TestCase
{
    /**
     * Checks that the [@see MappedHydrator] can create an instance of the
     * [@see Book] class.
     *
     * The [@see Book] class represents an entity that represents the aggregate
     * root for a whole bunch of related models.
     *
     * @test
     */
    function making_a_Book()
    {
        $title = "Fruit Infused Water: 50 Quick & Easy Recipes for Delicious & Healthy Hydration";

        /** @var Book $ourBook */
        $ourBook = $this->bookHydrator()->fromArray([
            "id" => '9781493634149',
            "book_title" => $title,
            "author_first_name" => "Elle",
            "author_last_name" => "Garner",
            "format" => "%s (by %s, ISBN %s)",
        ]);

        $this->assertSame(
            "$title (by Elle Garner, ISBN 9781493634149)",
            (string) $ourBook
        );
        $this->assertTrue($ourBook->wasWrittenByThe(Author::named("Elle", "Garner")));
        $this->assertTrue($ourBook->hasInItsTitle("Healthy Hydration"));
    }

    /** @test */
    function making_another_Book()
    {
        /** @var Book $ourBook */
        $ourBook = $this->bookHydrator()->fromArray([
            "id" => '9781420922530',
            "book_title" => 'Hamlet',
            "author_first_name" => "William",
            "author_last_name" => "Shakespeare",
            "format" => "%s (by %s, ISBN %s)",
        ]);

        $this->assertSame(
            "Hamlet (by William Shakespeare, ISBN 9781420922530)",
            (string) $ourBook
        );
        $this->assertTrue($ourBook->wasWrittenByThe(Author::named("William", "Shakespeare")));
        $this->assertTrue($ourBook->hasInItsTitle("Hamlet"));
    }

    /** @test */
    function notifying_the_observers()
    {
        $emptyObject = (new ReflectionClass(Title::class))->newInstanceWithoutConstructor();

        /** @var ObservesHydration|MockObject $observer */
        $observer = $this->createMock(ObservesHydration::class);
        $observer->expects($this->once())->method('hydrating')->with($emptyObject);

        $hydrator = MappedHydrator::forThe(
            Title::class,
            new Properties,
            null,
            $observer
        );

        $hydrator->fromArray(['foo' => 'bar']);
    }

    /** @test */
    function throwing_a_custom_exception_when_mapping_failed()
    {
        $exception = new Unmappable('Original exception message here.');
        $propertyMapping = $this->createMock(MapsProperty::class);
        $propertyMapping->expects($this->atLeastOnce())
            ->method('name')->willReturnCallback(function () use ($exception) {
                throw $exception;
            });
        $throw = new Properties($propertyMapping);

        $hydrator = MappedHydrator::forThe(Book::class, $throw);

        $this->expectException(CouldNotHydrate::class);
        $this->expectExceptionMessage(
            'Could not load the class `'.Book::class. '`: Original exception message here.'
        );

        $hydrator->fromArray(['foo' => 'bar']);
    }

    /**
     * Sets up the hydrator.
     *
     * This sets up one [@see MappedHydrator], which is working together with
     * several [@see SimpleHydrator]s, one for each of the test assets that are
     * needed to fully hydrate the [@see Book] class.
     */
    private function bookHydrator() : Hydrates
    {
        return MappedHydrator::forThe(Book::class, $this->bookMapping());
    }

    /**
     * Since mapping itself is out of scope for this unit test, the mapping is
     * defined through mocking the interfaces.
     *
     * @return MapsProperties
     */
    private function bookMapping() : MapsProperties
    {
        return new Properties(
            $this->mapObjectProperty('isbn',
                SimpleHydrator::forThe(Isbn::class),
                ['code' => 'id']
            ),
            $this->mapObjectProperty('title',
                SimpleHydrator::forThe(Title::class),
                ['title' => 'book_title']
            ),
            $this->mapObjectProperty('title',
                SimpleHydrator::forThe(Title::class),
                ['title' => 'book_title']
            ),
            $this->mapObjectProperty('author',
                SimpleHydrator::forThe(Author::class),
                [
                    'firstName' => 'author_first_name',
                    'lastName' => 'author_last_name',
                ]
            ),
            $this->mapEmptyObjectProperty('contents',
                VariadicConstructor::forThe(Contents::class)
            ),
            $this->mapScalarProperty('format', 'format')
        );
    }

    /**
     * @param string   $property    Property of the originally mapped object
     * @param Hydrates $hydrator    Hydrator for the related object
     * @param array    $map
     * @return MapsProperty|MockObject
     */
    private function mapObjectProperty(
        string $property,
        Hydrates $hydrator,
        array $map
    ) : MockObject
    {
        $mapper = $this->createMock(MapsProperty::class);
        $mapper->expects($this->atLeastOnce())
            ->method('name')->willReturn($property);
        $mapper->expects($this->atLeastOnce())
            ->method('value')
            ->willReturnCallback(
                function (array $data, $owner) use ($hydrator, $map) {
                    TestCase::assertTrue(is_object($owner),
                        'Expected the owner to be an object, got '.gettype($owner)
                    );
                    $array = [];
                    foreach ($map as $property => $key) {
                        $array[$property] = $data[$key];
                    }
                    return $hydrator->fromArray($array);
                }
            );
        return $mapper;
    }

    /**
     * @param string $property
     * @param string $key
     * @return MapsProperty|MockObject
     */
    private function mapScalarProperty(string $property, string $key) : MockObject
    {
        $mapper = $this->createMock(MapsProperty::class);
        $mapper->expects($this->atLeastOnce())
            ->method('name')->willReturn($property);
        $mapper->expects($this->atLeastOnce())
            ->method('value')
            ->willReturnCallback(function (array $data) use ($key) {
                return $data[$key];
            });
        return $mapper;
    }

    /**
     * @param string   $property      Property of the originally mapped object
     * @param Hydrates $hydrator      Hydrator for the related object
     * @param array    $hydrationData
     * @return MapsProperty|MockObject
     */
    private function mapEmptyObjectProperty(
        string $property,
        Hydrates $hydrator,
        array $hydrationData = []
    ) : MockObject
    {
        $mapper = $this->createMock(MapsProperty::class);
        $mapper->expects($this->atLeastOnce())
            ->method('name')->willReturn($property);
        $mapper->expects($this->atLeastOnce())
            ->method('value')
            ->willReturnCallback(function () use ($hydrator, $hydrationData) {
                return $hydrator->fromArray($hydrationData);
            });
        return $mapper;
    }
}
