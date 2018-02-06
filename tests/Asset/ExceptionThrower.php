<?php

declare(strict_types=1);

namespace Stratadox\Hydration\Test\Asset;

use Exception;

class ExceptionThrower
{
    private static $message;

    public function __construct()
    {
        throw new Exception(static::$message);
    }

    public static function setMessage(string $message)
    {
        static::$message = $message;
    }
}
