<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\AllCombined\Fixture;

final class NumberAttribute extends Attribute
{
    public function __construct(string $name, NumberValue $value)
    {
        parent::__construct($name, $value);
    }
}
