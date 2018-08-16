<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Data;

use Faker\Factory;
use Stratadox\Hydrator\ObjectHydrator;
use Stratadox\Hydrator\ReflectiveHydrator;

trait ExceptionMessages
{
    public function exceptionMessages(): array
    {
        $random = Factory::create();
        $sets = [];
        for ($i = 10; $i > 0; --$i) {
            $message = $random->sentence;
            $sets[$message] = [
                $random->randomElement([
                    ObjectHydrator::default(),
                    ReflectiveHydrator::default()
                ]),
                $message
            ];
        }
        return $sets;
    }
}
