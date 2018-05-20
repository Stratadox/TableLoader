<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason;

use function array_map;
use function assert;
use function implode;
use function is_array;

final class TextListAttribute extends Attribute
{
    private const MAKE_THEM_STRINGS = '\strval';

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

    public function value(): array
    {
        assert(is_array($this->value));
        return array_map(self::MAKE_THEM_STRINGS, $this->value);
    }
}
