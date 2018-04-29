<?php

namespace Stratadox\TableLoader\Test\Feature\AllCombined\Fixture;

use function count as thereIsA;
use function current as theCurrent;

abstract class Sellable
{
    protected $name;
    protected $prices;

    public function __construct(string $name, Prices $prices)
    {
        $this->name = $name;
        $this->prices = $prices;
    }

    /**
     * Retrieves the name of the sellable item.
     *
     * @return string The name of the item.
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Retrieves the price for the item in this currency.
     *
     * @param string $currency
     * @return Monetary
     * @throws NoPricingAvailable
     */
    public function priceIn(string $currency): Monetary
    {
        $price = $this->prices->that(AreOfTheCurrency::withCode($currency));
        if (thereIsA($price)) {
            return theCurrent($price);
        }
        throw NoPricingAvailable::inTheCurrency($this, $currency);
    }

    /**
     * Retrieves the string representation of the item.
     *
     * @return string The string representation of the sellable item.
     */
    public function __toString(): string
    {
        return $this->name();
    }
}
