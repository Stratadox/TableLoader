<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

abstract class Product
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
}
