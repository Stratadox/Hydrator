<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator\Test\Asset;

use RuntimeException;
use Stratadox\Hydrator\CannotHydrate;

class Unmappable extends RuntimeException implements CannotHydrate
{
}
