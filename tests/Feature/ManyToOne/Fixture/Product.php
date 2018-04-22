<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\ManyToOne\Fixture;

final class Product
{
    private $name;
    private $price;

    public function __construct(string $name, int $price)
    {
        $this->name = $name;
        $this->price = $price;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function price(): int
    {
        return $this->price;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
