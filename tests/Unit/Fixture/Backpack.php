<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

final class Backpack implements ContainsThings
{
    private $things;

    public function __construct(Thing ...$things)
    {
        $this->things = new Things(...$things);
    }

    public function things(): Things
    {
        return $this->things;
    }
}
