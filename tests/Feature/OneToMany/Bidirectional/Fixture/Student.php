<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\OneToMany\Bidirectional\Fixture;

final class Student
{
    private $name;
    private $books = [];

    public function __construct(Name $name)
    {
        $this->name = $name;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function get(Book $book): void
    {
        $this->books[] = $book;
    }

    /** @return Book[] */
    public function books(): array
    {
        return $this->books;
    }
}
