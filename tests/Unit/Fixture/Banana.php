<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

final class Banana extends Fruit
{
    private $curve;

    public function __construct(Country $countryOfOrigin, int $curve)
    {
        $this->curve = $curve;
        parent::__construct($countryOfOrigin);
    }

    public function curve(): int
    {
        return $this->curve;
    }
}
