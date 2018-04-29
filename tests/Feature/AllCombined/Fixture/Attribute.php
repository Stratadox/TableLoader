<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\AllCombined\Fixture;

use function sprintf;

abstract class Attribute implements ReasonToBuy
{
    protected $name;
    protected $value;

    public function __construct(string $name, $value)
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
}
