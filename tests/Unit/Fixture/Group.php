<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

final class Group
{
    private $members = [];
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function members(): array
    {
        return $this->members;
    }
}
