<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

final class Country
{
    private $name;
    private $continent;

    public function __construct(string $name, string $continent)
    {
        $this->name = $name;
        $this->continent = $continent;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function continent(): string
    {
        return $this->continent;
    }
}
