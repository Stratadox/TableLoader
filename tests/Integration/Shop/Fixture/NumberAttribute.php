<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Fixture;

use function assert;

final class NumberAttribute extends Attribute
{
    public function __construct(string $name, NumberValue $value)
    {
        parent::__construct($name, $value);
    }

    public function value(): int
    {
        assert($this->value instanceof NumberValue);
        return $this->value->value();
    }
}
