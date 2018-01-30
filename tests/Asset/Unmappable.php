<?php

declare(strict_types=1);

namespace Stratadox\Hydration\Test\Asset;

use RuntimeException;
use Stratadox\Hydrator\CouldNotHydrate;

class Unmappable extends RuntimeException implements CouldNotHydrate
{
}
