<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

use function assert;

final class Basket implements ContainsThings
{
    private $name;
    private $things;

    public function __construct(string $name, ?Things $things)
    {
        $this->name = $name;
        $this->things = $things;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function things(): Things
    {
        assert(isset($this->things));
        return $this->things;
    }
}
