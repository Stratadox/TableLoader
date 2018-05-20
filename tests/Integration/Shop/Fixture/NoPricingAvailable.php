<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Fixture;

use InvalidArgumentException;
use Throwable;

final class NoPricingAvailable extends InvalidArgumentException
{
    public static function inTheCurrency(Sellable $item, string $currency): Throwable
    {
        return new NoPricingAvailable("No `$currency` pricing available for the `$item`");
    }
}
