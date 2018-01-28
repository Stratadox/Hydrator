<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator\Test;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use Stratadox\Hydration\Hydrates;
use Stratadox\Hydration\Hydrator\MappedHydrator;
use Stratadox\Hydration\Hydrator\SimpleHydrator;
use Stratadox\Hydration\Hydrator\VariadicConstructor;
use Stratadox\Hydration\MapsObject;
use Stratadox\Hydration\MapsProperty;
use Stratadox\Hydration\Test\Asset\Book\Author;
use Stratadox\Hydration\Test\Asset\Book\Book;
use Stratadox\Hydration\Test\Asset\Book\Contents;
use Stratadox\Hydration\Test\Asset\Book\Isbn;
use Stratadox\Hydration\Test\Asset\Book\Title;
use Stratadox\Hydration\Test\Asset\Spy\SpyOnCurrentInstanceAsPropertyMapping;
use Stratadox\Hydration\Test\Asset\Unmappable;
use Stratadox\Hydration\UnmappableInput;

/**
 * @covers \Stratadox\Hydration\Hydrator\MappedHydrator
 * @covers \Stratadox\Hydration\Hydrator\CouldNotMap
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
     * @scenario
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

    /** @scenario */
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

    /** @scenario */
    function retrieving_the_currently_hydrating_instance()
    {
        $spy = SpyOnCurrentInstanceAsPropertyMapping::expectThe(Book::class);

        /** @var MockObject|MapsObject $map */
        $map = $this->createMock(MapsObject::class);
        $map->expects($this->once())->method('className')->willReturn(Book::class);
        $map->expects($this->once())->method('properties')->willReturn([$spy]);

        $hydrator = MappedHydrator::fromThis($map);

        $spy->onThe($hydrator);

        $this->assertNull(
            $hydrator->currentInstance(),
            'Not expecting a current instance yet.'
        );

        $hydrator->fromArray([]);

        $this->assertNull(
            $hydrator->currentInstance(),
            'Not expecting a current instance anymore.'
        );
    }

    /** @scenario */
    function throwing_a_custom_exception_when_mapping_failed()
    {
        $exception = new Unmappable('Original exception message here.');
        /** @var MockObject|MapsProperty $throw */
        $throw = $this->createMock(MapsProperty::class);
        $throw->expects($this->once())->method('value')->willReturnCallback(function () use ($exception) {
            throw $exception;
        });
        /** @var MockObject|MapsObject $map */
        $map = $this->createMock(MapsObject::class);
        $map->expects($this->once())->method('className')->willReturn(Book::class);
        $map->expects($this->once())->method('properties')->willReturn([$throw]);

        $hydrator = MappedHydrator::fromThis($map);

        $this->expectException(UnmappableInput::class);
        $this->expectExceptionMessage(
            'Could not map the class `'.Book::class. '`: Original exception message here.'
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
        return MappedHydrator::fromThis($this->bookMapping());
    }

    /**
     * Since mapping itself is out of scope for this unit test, the mapping is
     * defined through mocking the interfaces.
     *
     * @return MapsObject|Mock
     */
    private function bookMapping() : Mock
    {
        $map = $this->createMock(MapsObject::class);

        $map->expects($this->once())
            ->method('className')
            ->willReturn(Book::class);

        $map->expects($this->once())
            ->method('properties')
            ->willReturn([
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
            ]);

        return $map;
    }

    /**
     * @param string   $property    Property of the originally mapped object
     * @param Hydrates $hydrator    Hydrator for the related object
     * @param array    $map
     * @return MapsProperty|Mock
     */
    private function mapObjectProperty(
        string $property,
        Hydrates $hydrator,
        array $map
    ) : Mock
    {
        $mapper = $this->createMock(MapsProperty::class);
        $mapper->expects($this->atLeastOnce())
            ->method('name')->willReturn($property);
        $mapper->expects($this->atLeastOnce())
            ->method('value')
            ->willReturnCallback(function (array $data) use ($hydrator, $map) {
                $array = [];
                foreach ($map as $property => $key) {
                    $array[$property] = $data[$key];
                }
                return $hydrator->fromArray($array);
            });
        return $mapper;
    }

    /**
     * @param string $property
     * @param string $key
     * @return MapsProperty|Mock
     */
    private function mapScalarProperty(string $property, string $key) : Mock
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
     * @return MapsProperty|Mock
     */
    private function mapEmptyObjectProperty(
        string $property,
        Hydrates $hydrator,
        array $hydrationData = []
    ) : Mock
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
