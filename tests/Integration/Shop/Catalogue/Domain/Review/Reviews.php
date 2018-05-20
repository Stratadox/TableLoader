<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Review;

use Stratadox\ImmutableCollection\ImmutableCollection;

class Reviews extends ImmutableCollection
{
    public function __construct(Review ...$reviews)
    {
        parent::__construct(...$reviews);
    }

    public function current(): Review
    {
        return parent::current();
    }

    public function offsetGet($index): Review
    {
        return parent::offsetGet($index);
    }
}
