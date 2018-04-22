<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

final class Foo
{
    private $name;
    private $bars = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }

    /** @return Bar[] */
    public function bars(): array
    {
        return $this->bars;
    }
}
