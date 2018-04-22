<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\OneToMany\Unidirectional\Fixture;

final class Club
{
    private $name;
    private $memberList;

    private function __construct(
        string $name,
        MemberList $members
    ) {
        $this->name = $name;
        $this->memberList = $members;
    }

    public static function establishedBy(Member $founder, string $name): self
    {
        return new Club($name, MemberList::with($founder));
    }

    public function name(): string
    {
        return $this->name;
    }

    public function foundingMember(): Member
    {
        return $this->memberList[0];
    }

    public function hasAsMember(Member $member): bool
    {
        return $this->memberList->hasThe($member);
    }

    public function join(Member $member): void
    {
        $this->memberList = $this->memberList->add($member);
    }
}
