<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason;

final class NumberValue implements ReasonToBuy
{
    private $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value();
    }
}
