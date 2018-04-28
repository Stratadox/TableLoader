<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

final class Customer
{
    private $name;
    private $baskets;

    public function __construct(string $name, Basket ...$baskets)
    {
        $this->name = $name;
        $this->baskets = $baskets;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function baskets(): array
    {
        return $this->baskets;
    }
}
