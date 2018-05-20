<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\AllCombined\Fixture\Infrastructure;

use Stratadox\Collection\Collection;
use Stratadox\Proxy\Proxy;
use Stratadox\Proxy\Proxying;
use Stratadox\TableLoader\Test\Feature\AllCombined\Fixture\Review;
use Stratadox\TableLoader\Test\Feature\AllCombined\Fixture\Reviews;

class ReviewsProxy extends Reviews implements Proxy
{
    use Proxying;

    public function __construct()
    {
        parent::__construct();
    }

    public function items(): array
    {
        return $this->__load()->items();
    }

    protected function newCopy(array $items): Collection
    {
        return $this->__load()->newCopy($items);
    }

    public function current(): Review
    {
        return $this->__load()->current();
    }

    public function offsetGet($index): Review
    {
        return $this->__load()->offsetGet($index);
    }

    public function count()
    {
        return $this->__load()->count();
    }

    public function toArray()
    {
        return $this->__load()->toArray();
    }

    public function getSize()
    {
        return $this->__load()->getSize();
    }

    public function offsetExists($index)
    {
        return $this->__load()->offsetExists($index);
    }

    public function rewind()
    {
        return $this->__load()->rewind();
    }

    public function key()
    {
        return $this->__load()->key();
    }

    public function next()
    {
        return $this->__load()->next();
    }

    public function valid()
    {
        return $this->__load()->valid();
    }
}
