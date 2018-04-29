<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\AllCombined\Fixture;

use function implode;

final class TextListAttribute extends Attribute
{
    public function __construct(string $name, TextValue ...$values)
    {
        parent::__construct($name, $values);
    }

    public function __toString(): string
    {
        return sprintf(
            '%s: %s',
            $this->name,
            implode(', ', $this->value)
        );
    }
}
