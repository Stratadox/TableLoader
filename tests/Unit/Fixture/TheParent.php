<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

abstract class TheParent
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }
}
