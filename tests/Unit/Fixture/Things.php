<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

use Stratadox\ImmutableCollection\ImmutableCollection;

class Things extends ImmutableCollection
{
    public function __construct(Thing ...$things)
    {
        parent::__construct(...$things);
    }

    public function current(): Thing
    {
        return parent::current();
    }

    public function offsetGet($index): Thing
    {
        return parent::offsetGet($index);
    }
}
