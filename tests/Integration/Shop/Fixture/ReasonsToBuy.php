<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Fixture;

use Stratadox\Collection\ConversionFailed;
use Stratadox\Collection\Filterable;
use Stratadox\Collection\Implodable;
use Stratadox\ImmutableCollection\Filtering;
use Stratadox\ImmutableCollection\ImmutableCollection;
use Stratadox\ImmutableCollection\Imploding;

final class ReasonsToBuy extends ImmutableCollection implements Filterable, Implodable, ReasonToBuy
{
    use Filtering, Imploding;

    public function __construct(ReasonToBuy ...$prices)
    {
        parent::__construct(...$prices);
    }

    public function current(): ReasonToBuy
    {
        return parent::current();
    }

    public function offsetGet($offset): ReasonToBuy
    {
        return parent::offsetGet($offset);
    }

    public function __toString(): string
    {
        try {
            return $this->implode(PHP_EOL);
        } catch (ConversionFailed $exception) {
            return 'Temporarily unavailable.';
        }
    }
}
