<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Fixture;

use InvalidArgumentException;
use function preg_match;
use function strlen;

final class Colour
{
    private const VALID_PATTERN = '/^[#]?([a-f0-9]{3}[[a-f0-9]{3}]?)\b$/i';
    private $hexCode;

    private function __construct(string $hexCode)
    {
        /** @var string[] $colour */
        if (!preg_match(Colour::VALID_PATTERN, $hexCode, $colour)) {
            throw new InvalidArgumentException("Invalid colour code `$hexCode`.");
        }
        $code = $colour[1];
        if (strlen($code) === 3) {
            $code = $code[0] . $code[0] . $code[1] . $code[1] . $code[2] . $code[2];
        }
        $this->hexCode = $code;
    }

    public static function withCode(string $hexCode): Colour
    {
        return new Colour($hexCode);
    }

    public static function red(): Colour
    {
        return new Colour('F00');
    }

    public static function green(): Colour
    {
        return new Colour('0F0');
    }

    public static function blue(): Colour
    {
        return new Colour('00F');
    }

    public function equals(Colour $other): bool
    {
        return $this->hexCode === $other->hexCode;
    }

    public function hexCode(): string
    {
        return $this->hexCode;
    }

    public function __toString(): string
    {
        return "#{$this->hexCode}";
    }
}
