<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\ManyToMany\Fixture;

use Stratadox\Collection\Appendable;
use Stratadox\ImmutableCollection\Appending;
use Stratadox\ImmutableCollection\ImmutableCollection;

final class Students extends ImmutableCollection implements Appendable
{
    use Appending { add as register; }

    public function __construct(Student ...$students)
    {
        parent::__construct(...$students);
    }

    public function current(): Student
    {
        return parent::current();
    }

    public function offsetGet($index): Student
    {
        return parent::offsetGet($index);
    }
}
