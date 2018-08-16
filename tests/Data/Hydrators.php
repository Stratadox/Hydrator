<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Data;

use Stratadox\Hydrator\ObjectHydrator;
use Stratadox\Hydrator\ReflectiveHydrator;

trait Hydrators
{
    public function hydrators(): array
    {
        return [
            'Object hydrator' => [ObjectHydrator::default()],
            'Reflective hydrator' => [ReflectiveHydrator::default()],
        ];
    }
}
