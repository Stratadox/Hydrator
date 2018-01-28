<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator\Test;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydration\Hydrates;
use Stratadox\Hydration\Hydrator\OneOfTheseHydrators;
use Stratadox\Hydration\Hydrator\SimpleHydrator;
use Stratadox\Hydration\Test\Asset\Book\Book;
use Stratadox\Hydration\Test\Asset\Book\Image;
use Stratadox\Hydration\Test\Asset\Book\Text;
use Stratadox\Hydration\Test\Asset\Spy\SpyOnCurrentInstanceAsHydrator;
use Stratadox\Hydration\Test\Asset\Spy\SpyOnCurrentInstanceAsPropertyMapping;
use Stratadox\Hydration\UnmappableInput;
use TypeError;

/**
 * @covers \Stratadox\Hydration\Hydrator\OneOfTheseHydrators
 * @covers \Stratadox\Hydration\Hydrator\CannotDecideOnAHydrator
 */
class OneOfTheseHydrators_will_hydrate_my_data extends TestCase
{
    /** @scenario */
    function making_a_Text_Element()
    {
        /** @var Text $element */
        $element = $this->makeElement()->fromArray([
            'type' => 'text',
            'text' => 'Hello World'
        ]);

        $this->assertInstanceOf(Text::class, $element);
        $this->assertSame('Hello World', (string) $element);
    }

    /** @scenario */
    function making_an_Image_Element()
    {
        /** @var Image $element */
        $element = $this->makeElement()->fromArray([
            'type' => 'image',
            'src' => 'hello.jpg'
        ]);

        $this->assertInstanceOf(Image::class, $element);
        $this->assertSame('hello.jpg', (string) $element);
    }

    /** @scenario */
    function trying_to_make_an_undefined_class()
    {
        $this->expectException(UnmappableInput::class);
        $this->expectExceptionMessage('Invalid class decision key: `invalid`.');

        $this->makeElement()->fromArray(['type' => 'invalid']);
    }

    /** @scenario */
    function trying_to_use_data_that_misses_the_decision_key()
    {
        $this->expectException(UnmappableInput::class);
        $this->expectExceptionMessage('Missing class decision key: `type`.');

        $this->makeElement()->fromArray(['text' => 'irrelevant']);
    }

    /** @scenario */
    function trying_to_make_an_invalid_hydrator()
    {
        $this->expectException(TypeError::class);

        OneOfTheseHydrators::decideBasedOnThe('type', [
            'not-a-hydrator' => $this,
        ]);
    }

    /** @scenario */
    function retrieving_the_currently_hydrating_instance()
    {
        $spy = SpyOnCurrentInstanceAsHydrator::expectThe(Image::class);
        $hydrator = OneOfTheseHydrators::decideBasedOnThe('type', [
            'text' => $this->createMock(Hydrates::class),
            'image' => $spy,
        ]);
        $spy->onThe($hydrator);

        $this->assertNull(
            $hydrator->currentInstance(),
            'Not expecting a current instance yet.'
        );

        $hydrator->fromArray(['type' => 'image']);

        $this->assertNull(
            $hydrator->currentInstance(),
            'Not expecting a current instance anymore.'
        );
    }

    private function makeElement() : Hydrates
    {
        return OneOfTheseHydrators::decideBasedOnThe('type', [
            'text' => SimpleHydrator::forThe(Text::class),
            'image' => SimpleHydrator::forThe(Image::class),
        ]);
    }
}
