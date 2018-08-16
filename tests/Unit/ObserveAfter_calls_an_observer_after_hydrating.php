<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Unit;

use Exception;
use PHPUnit\Framework\TestCase;
use Stratadox\Hydrator\Hydrates;
use Stratadox\Hydrator\ObjectHydrator;
use Stratadox\Hydrator\ObserveAfter;
use Stratadox\Hydrator\Test\Data\Hydrators;
use Stratadox\Hydrator\Test\Fixture\Observer;
use Stratadox\Hydrator\Test\Fixture\Popo;

/**
 * @covers \Stratadox\Hydrator\ObserveAfter
 * @covers \Stratadox\Hydrator\Observe
 */
class ObserveAfter_calls_an_observer_after_hydrating extends TestCase
{
    use Hydrators;

    /**
     * @test
     * @dataProvider hydrators
     */
    function notifying_the_observer_upon_hydration(Hydrates $hydrator)
    {
        $observer = new Observer;
        $observedHydrator = ObserveAfter::hydrating($hydrator, $observer);
        $object = new Popo;

        $observedHydrator->writeTo($object, ['foo' => 'bar']);

        $this->assertCount(1, $observer->observedInstances());
        $this->assertCount(1, $observer->observedDataSets());

        $this->assertSame($object, $observer->observedInstance());
        $this->assertSame(['foo' => 'bar'], $observer->observedDataSet());

        $this->assertAttributeEquals('bar', 'foo', $object);
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
            $this->assertSame(
                "Could not hydrate the `$popo`: This is expected.",
                $exception->getMessage()
            );

            $this->assertCount(0, $observer->observedInstances());
            return;
        }
        $this->fail();
    }
}
