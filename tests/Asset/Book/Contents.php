<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Asset\Book;

use Stratadox\ImmutableCollection\ImmutableCollection;
use function implode;

class Contents extends ImmutableCollection
{
    public function __construct(Chapter ...$chapters)
    {
        parent::__construct(...$chapters);
    }

    public static function are(Chapter ...$chapters)
    {
        return new static(...$chapters);
    }

    public function textInChapter(int $index): Text
    {
        return $this->textFromChapterAt($this[$index]);
    }

    public function textFromChapterAt(Chapter $selected): Text
    {
        return $selected->text();
    }

    public function __toString(): string
    {
        return implode("\n\n", $this->toArray());
    }
}
