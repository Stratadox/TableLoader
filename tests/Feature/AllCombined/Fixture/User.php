<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\AllCombined\Fixture;

abstract class User
{
    protected $name;

    public function __construct(Username $name)
    {
        $this->name = $name;
    }

    public function name(): Username
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}
