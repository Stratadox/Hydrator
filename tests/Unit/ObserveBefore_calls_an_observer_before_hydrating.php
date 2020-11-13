<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Unit;

use Exception;
use PHPUnit\Framework\TestCase;
use Stratadox\Hydrator\Hydrator;
use Stratadox\Hydrator\ObjectHydrator;
use Stratadox\Hydrator\ObserveBefore;
use Stratadox\Hydrator\Test\Data\Hydrators;
use Stratadox\Hydrator\Test\Fixture\Observer;
use Stratadox\Hydrator\Test\Fixture\Popo;

class ObserveBefore_calls_an_observer_before_hydrating extends TestCase
{
    use Hydrators;

    /**
     * @test
     * @dataProvider hydrators
     */
    function notifying_the_observer_upon_hydration(Hydrator $hydrator)
    {
        $observer = new Observer;
        $observedHydrator = ObserveBefore::hydrating($hydrator, $observer);
        $object = new Popo;

        $observedHydrator->writeTo($object, ['foo' => 'bar']);

        self::assertCount(1, $observer->observedInstances());
        self::assertCount(1, $observer->observedDataSets());

        self::assertSame($object, $observer->observedInstance());
        self::assertSame(['foo' => 'bar'], $observer->observedDataSet());

        self::assertAttributeEquals('bar', 'foo', $object);
    }

    /** @test */
    function notifying_the_observer_before_hydrating_throws_an_exception()
    {
        $observer = new Observer;
        $observedHydrator = ObserveBefore::hydrating(
            ObjectHydrator::using(function () {
                throw new Exception('Expected');
            }),
            $observer
        );
        $object = new Popo;

        try {
            $observedHydrator->writeTo($object, ['foo' => 'bar']);
        } catch (Exception $exception) {
            $popo = Popo::class;
            self::assertSame(
                "Could not hydrate the `$popo`: Expected",
                $exception->getMessage()
            );

            self::assertCount(1, $observer->observedInstances());
            self::assertSame($object, $observer->observedInstance(0));
            return;
        }
        self::fail();
    }
}
