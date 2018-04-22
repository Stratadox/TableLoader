<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\ManyToOne\Fixture;

final class OrderLine
{
    private $product;
    private $quantity;

    public function __construct(Product $product, int $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function product(): Product
    {
        return $this->product;
    }

    public function totalPrice(): int
    {
        return $this->product->price() * $this->quantity;
    }
}
