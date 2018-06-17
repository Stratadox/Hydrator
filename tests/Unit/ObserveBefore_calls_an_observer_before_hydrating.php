<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Unit;

use Exception;
use PHPUnit\Framework\TestCase;
use Stratadox\Hydrator\Hydrates;
use Stratadox\Hydrator\ObjectHydrator;
use Stratadox\Hydrator\ObserveBefore;
use Stratadox\Hydrator\ReflectiveHydrator;
use Stratadox\Hydrator\Test\Fixture\Observer;
use Stratadox\Hydrator\Test\Fixture\Popo;

/**
 * @covers \Stratadox\Hydrator\ObserveBefore
 * @covers \Stratadox\Hydrator\Observe
 */
class ObserveBefore_calls_an_observer_before_hydrating extends TestCase
{
    /**
     * @test
     * @dataProvider hydrators
     */
    function notifying_the_observer_upon_hydration(Hydrates $hydrator)
    {
        $observer = new Observer;
        $observedHydrator = ObserveBefore::hydrating($hydrator, $observer);
        $object = new Popo;

        $observedHydrator->writeTo($object, ['foo' => 'bar']);

        $this->assertCount(1, $observer->observedInstances());
        $this->assertCount(1, $observer->observedDataSets());

        $this->assertSame($object, $observer->observedInstance(0));
        $this->assertSame(['foo' => 'bar'], $observer->observedDataSet(0));

        $this->assertAttributeEquals('bar', 'foo', $object);
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
            $this->fail();
        } catch (Exception $exception) {
            $popo = Popo::class;
            $this->assertSame(
                "Could not hydrate the `$popo`: Expected",
                $exception->getMessage()
            );

            $this->assertCount(1, $observer->observedInstances());
            $this->assertSame($object, $observer->observedInstance(0));
        }
    }

    public function hydrators(): array
    {
        return [
            'Object hydrator' => [ObjectHydrator::default()],
            'Reflective hydrator' => [ReflectiveHydrator::default()],
        ];
    }
}
