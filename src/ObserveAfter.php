<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

final class ObserveAfter extends Observe
{
    public function writeTo(object $target, array $input): void
    {
        $this->hydrate($target, $input);
        $this->observe($target, $input);
    }
}
