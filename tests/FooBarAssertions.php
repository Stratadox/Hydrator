<?php

declare(strict_types=1);

namespace Stratadox\Hydrator\Test;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\AssertionFailedError;
use Stratadox\Hydration\Test\Asset\FooBar\Foo;

trait FooBarAssertions
{
    /** @throws AssertionFailedError */
    protected function assertBaz(
        string $expectedBaz,
        Foo $theFooInQuestion,
        string $message = ''
    ) : void
    {
        $this->assertSame($expectedBaz, $theFooInQuestion->baz(), $message);
    }

    /** @see Assert::assertSame */
    abstract public static function assertSame(
        $expected,
        $actual,
        $message = ''
    );
}
