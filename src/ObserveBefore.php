<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

final class ObserveBefore extends Observe
{
    public function writeTo(object $target, array $input): void
    {
        $this->observe($target, $input);
        $this->hydrate($target, $input);
    }
}
