<?php

declare(strict_types = 1);

namespace Stratadox\Hydrator\Test\Asset\Book;

use function implode;
use Stratadox\ImmutableCollection\ImmutableCollection;

class Chapter extends ImmutableCollection
{
    private $title;

    public function __construct(Title $title, Element ...$elements)
    {
        $this->title = $title;
        parent::__construct(...$elements);
    }

    public static function titled(
        string $title,
        Element ...$elements
    ): Chapter {
        return new static(new Title($title), ...$elements);
    }

    public function title(): Title
    {
        return $this->title;
    }

    public function text(): Text
    {
        $text = Text::startEmpty();
        foreach ($this as $element) {
            if ($element instanceof Text) {
                $text->add($element);
            }
        }
        return $text;
    }

    public function __toString(): string
    {
        return "{$this->title()}\n\n" . implode("\n\n", $this->toArray());
    }
}
