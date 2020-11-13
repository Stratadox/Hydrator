<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Integration;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydrator\MappedHydrator;
use Stratadox\Hydrator\ObjectHydrator;
use Stratadox\Hydrator\ObserveAfter;
use Stratadox\Hydrator\ObserveBefore;
use Stratadox\Hydrator\Test\Fixture\Observer;
use Stratadox\Hydrator\Test\Fixture\Popo;
use Stratadox\Hydrator\Test\Fixture\Rename;

/**
 * @coversNothing
 */
class Combining_decorators extends TestCase
{
    /** @test */
    function observing_before_and_after_mapping()
    {
        $innerBeforeObserver = new Observer;
        $innerAfterObserver = new Observer;
        $outerBeforeObserver = new Observer;
        $outerAfterObserver = new Observer;
        $object = new Popo;

        $hydrator =
            ObserveAfter::hydrating(
                ObserveBefore::hydrating(
                    MappedHydrator::using(
                        ObserveAfter::hydrating(
                            ObserveBefore::hydrating(
                                ObjectHydrator::default(),
                                $innerBeforeObserver
                            ),
                            $innerAfterObserver
                        ),
                        Rename::between('data_foo', 'foo'),
                        Rename::between('data_bar', 'bar')
                    ),
                    $outerBeforeObserver
                ),
                $outerAfterObserver
            );

        $hydrator->writeTo($object, [
            'data_foo' => 'Foo.',
            'data_bar' => 'Bar.',
        ]);

        $this->assertSame([
            'foo' => 'Foo.',
            'bar' => 'Bar.',
        ], $innerBeforeObserver->observedDataSet());

        $this->assertSame([
            'foo' => 'Foo.',
            'bar' => 'Bar.',
        ], $innerAfterObserver->observedDataSet());

        $this->assertSame([
            'data_foo' => 'Foo.',
            'data_bar' => 'Bar.',
        ], $outerBeforeObserver->observedDataSet());

        $this->assertSame([
            'data_foo' => 'Foo.',
            'data_bar' => 'Bar.',
        ], $outerAfterObserver->observedDataSet());
    }
}
