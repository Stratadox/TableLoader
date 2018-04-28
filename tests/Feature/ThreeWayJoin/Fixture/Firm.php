<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\ThreeWayJoin\Fixture;

final class Firm
{
    private $name;
    private $lawyers;

    public function __construct(string $name, Lawyer ...$lawyers)
    {
        $this->name = $name;
        $this->lawyers = $lawyers;
    }

    public function name(): string
    {
        return $this->name;
    }

    /** @return Lawyer[] */
    public function lawyers(): array
    {
        return $this->lawyers;
    }
}
