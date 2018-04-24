<?php
declare(strict_types=1);

namespace Stratadox\Hydrator;

use function is_null;

/**
 * Null object for *not* observing the hydration process.
 *
 * @package Stratadox\Hydrate
 * @author  Stratadox
 */
final class BlindObserver implements ObservesHydration
{
    private static $cache;

    private function __construct()
    {
    }

    /**
     * Creates a new blind observer.
     *
     * @return ObservesHydration
     */
    public static function asDefault(): ObservesHydration
    {
        if (is_null(self::$cache)) {
            self::$cache = new self;
        }
        return self::$cache;
    }

    /** @inheritdoc */
    public function hydrating($theInstance): void
    {
        // No operation.
    }
}
