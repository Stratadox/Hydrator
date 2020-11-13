<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Unit;

use Exception;
use PHPUnit\Framework\TestCase;
use Stratadox\Hydrator\Hydrator;
use Stratadox\Hydrator\ObjectHydrator;
use Stratadox\Hydrator\ObserveAfter;
use Stratadox\Hydrator\Test\Data\Hydrators;
use Stratadox\Hydrator\Test\Fixture\Observer;
use Stratadox\Hydrator\Test\Fixture\Popo;

class ObserveAfter_calls_an_observer_after_hydrating extends TestCase
{
    use Hydrators;

    /**
     * @test
     * @dataProvider hydrators
     */
    function notifying_the_observer_upon_hydration(Hydrator $hydrator)
    {
        $observer = new Observer;
        $observedHydrator = ObserveAfter::hydrating($hydrator, $observer);
        $object = new Popo;

        $observedHydrator->writeTo($object, ['foo' => 'bar']);

        self::assertCount(1, $observer->observedInstances());
        self::assertCount(1, $observer->observedDataSets());

        self::assertSame($object, $observer->observedInstance());
        self::assertSame(['foo' => 'bar'], $observer->observedDataSet());

        self::assertEquals('bar', $object->foo ?? '');
    }

    /** @test */
    function not_notifying_the_observer_if_hydrating_threw_an_exception()
    {
        $observer = new Observer;
        $observedHydrator = ObserveAfter::hydrating(
            ObjectHydrator::using(function () {
                throw new Exception('This is expected.');
            }),
            $observer
        );
        $object = new Popo;

        try {
            $observedHydrator->writeTo($object, ['foo' => 'bar']);
        } catch (Exception $exception) {
            $popo = Popo::class;
            self::assertSame(
                "Could not hydrate the `$popo`: This is expected.",
                $exception->getMessage()
            );

            self::assertCount(0, $observer->observedInstances());
            return;
        }
        self::fail();
    }
}
