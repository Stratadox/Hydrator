<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

/**
 * Attaches an observer that gets invoked before hydrating the instance.
 *
 * @author Stratadox
 */
final class ObserveBefore extends Observe
{
    /** @inheritdoc */
    public function writeTo(object $target, array $input): void
    {
        $this->observe($target, $input);
        $this->hydrate($target, $input);
    }
}
