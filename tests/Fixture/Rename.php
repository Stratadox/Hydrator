<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Fixture;

use Stratadox\HydrationMapping\Mapping;

final class Rename implements Mapping
{
    private $from;
    private $to;

    public function __construct(string $from, string $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public static function between(string $from, string $to): Mapping
    {
        return new Rename($from, $to);
    }

    public function name(): string
    {
        return $this->to;
    }

    public function value(array $data, $owner = null)
    {
        return $data[$this->from];
    }
}
