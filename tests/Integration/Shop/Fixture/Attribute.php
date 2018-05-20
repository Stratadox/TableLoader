<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Fixture;

use function sprintf;

abstract class Attribute implements ReasonToBuy
{
    protected $name;
    protected $value;

    public function __construct(string $name, ReasonToBuy $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s: %s',
            $this->name,
            $this->value
        );
    }

    public function name(): string
    {
        return $this->name;
    }

    public function value()
    {
        return $this->value;
    }
}
