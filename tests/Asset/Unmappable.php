<?php

declare(strict_types=1);

namespace Stratadox\Hydration\Test\Asset;

use RuntimeException;
use Stratadox\Hydration\UnmappableInput;

class Unmappable extends RuntimeException implements UnmappableInput
{
}
