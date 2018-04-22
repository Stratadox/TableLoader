<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

use InvalidArgumentException;
use Stratadox\HydrationMapping\MapsProperties;
use Stratadox\HydrationMapping\MapsProperty;
use Stratadox\ImmutableCollection\ImmutableCollection;

final class Exceptional extends ImmutableCollection implements MapsProperties
{
    public static function mapping(string ...$messages): self
    {
        return new self(...$messages);
    }

    public function offsetGet($index): MapsProperty
    {
        throw new InvalidArgumentException(parent::offsetGet($index));
    }

    public function current(): MapsProperty
    {
        throw new InvalidArgumentException(parent::current());
    }
}
