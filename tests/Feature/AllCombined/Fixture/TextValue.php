<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\AllCombined\Fixture;

final class TextValue implements ReasonToBuy
{
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
