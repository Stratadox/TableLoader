<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

final class Member
{
    private $group;
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function group(): Group
    {
        return $this->group;
    }
}
