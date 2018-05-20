<?php

namespace Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain;

use function count as thereIsA;
use function current as theCurrent;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Price\AreOfTheCurrency;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Price\Monetary;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Price\NoPricingAvailable;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Price\Prices;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Review\Reviews;

abstract class Sellable
{
    protected $name;
    protected $prices;
    protected $reviews;

    public function __construct(string $name, Prices $prices, Reviews $reviews)
    {
        $this->name = $name;
        $this->prices = $prices;
        $this->reviews = $reviews;
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
     * Retrieves the reviews.
     *
     * @return Reviews
     */
    public function reviews(): Reviews
    {
        return $this->reviews;
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
