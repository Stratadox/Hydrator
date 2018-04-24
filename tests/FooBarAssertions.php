<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;
use Stratadox\Hydrator\Test\Asset\FooBar\Foo;

trait FooBarAssertions
{
    /** @throws ExpectationFailedException */
    protected function assertBaz(
        string $expectedBaz,
        Foo $theFooInQuestion,
        string $message = ''
    ): void {
        $this->assertSame($expectedBaz, $theFooInQuestion->baz(), $message);
    }

    /**
     * @see Assert::assertSame
     * @throws ExpectationFailedException
     */
    abstract public static function assertSame(
        $expected,
        $actual,
        string $message = ''
    ): void;
}
