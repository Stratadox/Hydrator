<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Fixture;

use Exception;
use Stratadox\HydrationMapping\MapsProperty;
use Stratadox\HydrationMapping\UnmappableInput;

final class Throwing implements MapsProperty
{
    private $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public static function withMessage(string $message): Throwing
    {
        return new self($message);
    }

    public function name(): string
    {
        return 'something';
    }

    public function value(array $data, $owner = null)
    {
        throw new class($this->message) extends Exception implements UnmappableInput {};
    }
}
