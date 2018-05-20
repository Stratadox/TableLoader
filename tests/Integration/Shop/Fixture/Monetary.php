<?php

namespace Stratadox\TableLoader\Test\Integration\Shop\Fixture;

interface Monetary
{
    /**
     * Retrieves the amount.
     *
     * @return string The amount.
     */
    public function amount(): string;

    /**
     * Retrieves the amount in cents.
     *
     * @return int The amount in cents.
     */
    public function cents(): int;

    /**
     * Retrieves the currency.
     *
     * @return string The currency code.
     */
    public function currency(): string;

    /**
     * Retrieves the string representation of the monetary amount.
     *
     * @return string The string representation of the amount.
     */
    public function __toString(): string;
}
