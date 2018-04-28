<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

final class Apple extends Fruit
{
    private $colour;

    public function __construct(Country $countryOfOrigin, string $colour)
    {
        $this->colour = $colour;
        parent::__construct($countryOfOrigin);
    }

    public function colour(): string
    {
        return $this->colour;
    }
}
