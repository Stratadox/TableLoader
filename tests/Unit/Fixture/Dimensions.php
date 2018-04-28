<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

final class Dimensions
{
    private $height;
    private $width;
    private $depth;

    public function __construct(int $height, int $width, int $depth)
    {
        $this->height = $height;
        $this->width = $width;
        $this->depth = $depth;
    }

    public function height(): int
    {
        return $this->height;
    }

    public function width(): int
    {
        return $this->width;
    }

    public function depth(): int
    {
        return $this->depth;
    }
}
