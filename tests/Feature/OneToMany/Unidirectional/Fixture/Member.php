<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\OneToMany\Unidirectional\Fixture;

final class Member
{
    private $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function named(string $name): self
    {
        return new self($name);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function isMemberOf(Club $club): bool
    {
        return $club->hasAsMember($this);
    }

    public function establish(string $name): Club
    {
        return Club::establishedBy($this, $name);
    }

    public function join(Club $club): void
    {
        $club->join($this);
    }
}
