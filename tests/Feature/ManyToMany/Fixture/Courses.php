<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\ManyToMany\Fixture;

use Stratadox\Collection\Appendable;
use Stratadox\Collection\Searchable;
use Stratadox\ImmutableCollection\Appending;
use Stratadox\ImmutableCollection\ImmutableCollection;
use Stratadox\ImmutableCollection\Searching;

final class Courses extends ImmutableCollection implements Appendable, Searchable
{
    use Appending { add as subscribeTo; }
    use Searching { hasThe as includes; }

    public function __construct(Course ...$courses)
    {
        parent::__construct(...$courses);
    }

    public function current(): Course
    {
        return parent::current();
    }

    public function offsetGet($index): Course
    {
        return parent::offsetGet($index);
    }
}
