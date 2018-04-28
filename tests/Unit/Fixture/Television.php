<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

final class Television extends Product
{
    private $brand;
    private $size;

    public function __construct(
        string $name,
        int $price,
        string $brand,
        Dimensions $size
    ) {
        parent::__construct($name, $price);
        $this->brand = $brand;
        $this->size = $size;
    }

    public function brand(): string
    {
        return $this->brand;
    }

    public function size(): Dimensions
    {
        return $this->size;
    }
}
