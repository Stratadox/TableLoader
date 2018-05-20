<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Price;

use Stratadox\Collection\Filterable;
use Stratadox\Collection\Implodable;
use Stratadox\ImmutableCollection\Filtering;
use Stratadox\ImmutableCollection\ImmutableCollection;
use Stratadox\ImmutableCollection\Imploding;

final class Prices extends ImmutableCollection implements Filterable, Implodable
{
    use Filtering, Imploding;

    public function __construct(Monetary ...$prices)
    {
        parent::__construct(...$prices);
    }

    public function current(): Monetary
    {
        return parent::current();
    }

    public function offsetGet($offset): Monetary
    {
        return parent::offsetGet($offset);
    }
}
