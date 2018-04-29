<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\AllCombined\Fixture;

final class TextAttribute extends Attribute
{
    public function __construct(string $name, TextValue $value)
    {
        parent::__construct($name, $value);
    }
}
