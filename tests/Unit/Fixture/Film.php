<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

final class Film extends Product
{
    private $producer;
    private $genre;

    public function __construct(
        string $name,
        int $price,
        string $producer,
        Genre $genre
    ) {
        parent::__construct($name, $price);
        $this->producer = $producer;
        $this->genre = $genre;
    }

    public function producer(): string
    {
        return $this->producer;
    }

    public function genre(): Genre
    {
        return $this->genre;
    }
}
