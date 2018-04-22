<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\OneToMany\Bidirectional\Fixture;

final class Book
{
    private $owner;
    private $title;

    public function __construct(Student $owner, string $title)
    {
        $this->owner = $owner;
        $this->title = $title;
    }

    public function owner(): Student
    {
        return $this->owner;
    }

    public function title(): string
    {
        return $this->title;
    }
}
