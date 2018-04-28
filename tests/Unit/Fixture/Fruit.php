<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

abstract class Fruit
{
    private $countryOfOrigin;

    public function __construct(Country $countryOfOrigin)
    {
        $this->countryOfOrigin = $countryOfOrigin;
    }

    public function countryOfOrigin(): Country
    {
        return $this->countryOfOrigin;
    }
}
