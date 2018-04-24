<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator\Test;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydrator\CannotHydrate;
use Stratadox\Hydrator\Test\Asset\Book\Image;
use Stratadox\Hydrator\Test\Asset\Book\Text;
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
    /** @var Hydrates */
    private $makeAnElement;

    protected function setUp(): void
    {
        $this->makeAnElement = OneOfTheseHydrators::decideBasedOnThe('type', [
            'text' => SimpleHydrator::forThe(Text::class),
            'image' => SimpleHydrator::forThe(Image::class),
        ]);
    }

    /** @test */
    function making_a_Text_Element()
    {
        /** @var Text $element */
        $element = $this->makeAnElement->fromArray([
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
        $element = $this->makeAnElement->fromArray([
            'type' => 'image',
            'src' => 'hello.jpg'
        ]);

        $this->assertInstanceOf(Image::class, $element);
        $this->assertSame('hello.jpg', (string) $element);
    }

    /** @test */
    function determining_that_it_will_be_the_Text_class()
    {
        $this->assertSame(Text::class, $this->makeAnElement->classFor([
            'type' => 'text',
            'text' => 'Hello World'
        ]));
    }

    /** @test */
    function determining_that_it_will_be_the_Image_class()
    {
        $this->assertSame(Image::class, $this->makeAnElement->classFor([
            'type' => 'image',
            'src' => 'hello.jpg'
        ]));
    }

    /** @test */
    function trying_to_make_an_undefined_class()
    {
        $this->expectException(CannotHydrate::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('Invalid class decision key: `invalid`.');

        $this->makeAnElement->fromArray(['type' => 'invalid']);
    }

    /** @test */
    function trying_to_use_data_that_misses_the_decision_key()
    {
        $this->expectException(CannotHydrate::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('Missing class decision key: `type`.');

        $this->makeAnElement->fromArray(['text' => 'irrelevant']);
    }

    /** @test */
    function trying_to_make_an_invalid_hydrator()
    {
        $this->expectException(TypeError::class);

        OneOfTheseHydrators::decideBasedOnThe('type', [
            'not-a-hydrator' => $this,
        ]);
    }
}
