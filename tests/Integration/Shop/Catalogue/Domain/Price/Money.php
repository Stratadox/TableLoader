<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Price;

use function bcdiv as divide;
use function sprintf;
use function strtoupper as upperCase;

final class Money implements Monetary
{
    private $amount;
    private $currency;

    public function __construct(int $amount, string $currency)
    {
        $this->amount = $amount;
        $this->currency = upperCase($currency);
    }

    public function amount(): string
    {
        return divide((string) $this->amount, '100', 2);
    }

    public function cents(): int
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s %s',
            $this->currency(),
            $this->amount()
        );
    }
}
