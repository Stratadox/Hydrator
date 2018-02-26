<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator\Test;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydrator\Test\Asset\Book\Image;
use Stratadox\Hydrator\Test\Asset\Book\Text;
use Stratadox\Hydrator\CouldNotHydrate;
use Stratadox\Hydrator\Hydrates;
use Stratadox\Hydrator\OneOfTheseHydrators;
use Stratadox\Hydrator\SimpleHydrator;
use TypeError;

/**
 * @covers \Stratadox\Hydrator\OneOfTheseHydrators
 * @covers \Stratadox\Hydrator\CannotDecideOnAHydrator
 */
class OneOfTheseHydrators_will_hydrate_my_data extends TestCase
{
    /** @test */
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

    /** @test */
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

    /** @test */
    function trying_to_make_an_undefined_class()
    {
        $this->expectException(CouldNotHydrate::class);
        $this->expectExceptionMessage('Invalid class decision key: `invalid`.');

        $this->makeElement()->fromArray(['type' => 'invalid']);
    }

    /** @test */
    function trying_to_use_data_that_misses_the_decision_key()
    {
        $this->expectException(CouldNotHydrate::class);
        $this->expectExceptionMessage('Missing class decision key: `type`.');

        $this->makeElement()->fromArray(['text' => 'irrelevant']);
    }

    /** @test */
    function trying_to_make_an_invalid_hydrator()
    {
        $this->expectException(TypeError::class);

        OneOfTheseHydrators::decideBasedOnThe('type', [
            'not-a-hydrator' => $this,
        ]);
    }

    private function makeElement(): Hydrates
    {
        return OneOfTheseHydrators::decideBasedOnThe('type', [
            'text' => SimpleHydrator::forThe(Text::class),
            'image' => SimpleHydrator::forThe(Image::class),
        ]);
    }
}
