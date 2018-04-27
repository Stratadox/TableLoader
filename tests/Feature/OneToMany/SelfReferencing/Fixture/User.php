<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\OneToMany\SelfReferencing\Fixture;

use function in_array as isItA;

final class User
{
    private $name;
    private $friends;

    public function __construct(string $name, User ...$friends)
    {
        $this->name = $name;
        $this->friends = $friends;
    }

    public function befriend(User $newFriend): void
    {
        $this->friends[] = $newFriend;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function friends(): array
    {
        return $this->friends;
    }

    public function isFriendOf(User $friend): bool
    {
        return isItA($friend, $this->friends, true);
    }
}
