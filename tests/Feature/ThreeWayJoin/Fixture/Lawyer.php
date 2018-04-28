<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\ThreeWayJoin\Fixture;

final class Lawyer
{
    private $name;
    private $clients;

    public function __construct(string $name, Client ...$clients)
    {
        $this->name = $name;
        $this->clients = $clients;
    }

    public function name(): string
    {
        return $this->name;
    }

    /** @return Client[] */
    public function clients(): array
    {
        return $this->clients;
    }
}
