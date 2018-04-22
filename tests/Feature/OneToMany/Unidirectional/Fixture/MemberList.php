<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\OneToMany\Unidirectional\Fixture;

use Stratadox\Collection\Appendable;
use Stratadox\Collection\Searchable;
use Stratadox\ImmutableCollection\Appending;
use Stratadox\ImmutableCollection\ImmutableCollection;
use Stratadox\ImmutableCollection\Searching;

final class MemberList extends ImmutableCollection implements Appendable, Searchable
{
    use Appending, Searching;

    public function __construct(Member ...$members)
    {
        parent::__construct(...$members);
    }

    public static function with(Member ...$members): self
    {
        return new self(...$members);
    }

    public function current(): Member
    {
        return parent::current();
    }

    public function offsetGet($offset): Member
    {
        return parent::offsetGet($offset);
    }
}
