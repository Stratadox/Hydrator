<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

/**
 * Attaches an observer that gets invoked after hydrating the instance.
 *
 * @author Stratadox
 */
final class ObserveAfter extends Observe
{
    /** @inheritdoc */
    public function writeTo(object $target, array $input): void
    {
        $this->hydrate($target, $input);
        $this->observe($target, $input);
    }
}
