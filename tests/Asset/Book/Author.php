<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Asset\Book;

class Author
{
    private $firstName;
    private $lastName;

    private function __construct(string $firstName, string $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public static function named(string $firstName, string $lastName): self
    {
        return new Author($firstName, $lastName);
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function __toString(): string
    {
        return "{$this->firstName()} {$this->lastName()}";
    }
}
