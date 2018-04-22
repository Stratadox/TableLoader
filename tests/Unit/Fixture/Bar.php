<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

final class Bar
{
    private $name;
    private $foos = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }

    /** @return Foo[] */
    public function foos(): array
    {
        return $this->foos;
    }
}
