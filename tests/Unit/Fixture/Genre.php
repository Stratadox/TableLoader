<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

final class Genre
{
    private $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function horror(): self
    {
        return new self('horror');
    }

    public static function comedy(): self
    {
        return new self('comedy');
    }

    public static function adventure(): self
    {
        return new self('adventure');
    }

    public function name(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name();
    }
}
