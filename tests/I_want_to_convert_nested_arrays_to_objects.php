<?php

namespace Stratadox\Hydrator\Test;

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

class I_want_to_convert_nested_arrays_to_objects extends TestCase
{
    /** @var Hydrates */
    private $makeNewBook;

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
        $ourBook = $this->makeNewBook->fromArray([
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

    /**
     * Sets up the hydrator.
     *
     * This sets up one [@see MappedHydrator], which is working together with
     * several [@see SimpleHydrator]s, one for each of the test assets that are
     * needed to fully hydrate the [@see Book] class.
     */
    protected function setUp()
    {
        $this->makeNewBook = MappedHydrator::fromThis($this->bookMapping());
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
            ->willReturnCallback(function (array $data) use ($hydrator, $map)
            {
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
            ->willReturnCallback(function (array $data) use ($key)
            {
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
            ->willReturnCallback(function () use ($hydrator, $hydrationData)
            {
                return $hydrator->fromArray($hydrationData);
            });
        return $mapper;
    }
}
