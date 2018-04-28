<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\ThreeWayJoin\Fixture;

final class Client
{
    private $name;
    private $value;

    public function __construct(string $name, int $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function value(): int
    {
        return $this->value;
    }
}
