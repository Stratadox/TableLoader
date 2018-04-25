<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\IdentityMap\IdentityMap;
use Stratadox\TableLoader\From;
use Stratadox\TableLoader\HasMany;
use Stratadox\TableLoader\HasOne;
use Stratadox\TableLoader\Identified;
use Stratadox\TableLoader\Result;
use Stratadox\TableLoader\Test\Unit\Fixture\Group;
use Stratadox\TableLoader\Test\Unit\Fixture\Member;
use Stratadox\TableLoader\To;
use Stratadox\TableLoader\Wire;
use Stratadox\TableLoader\Wired;

/**
 * @covers \Stratadox\TableLoader\Wired
 */
class Wired_objects_have_relationships extends TestCase
{
    /** @test */
    function wiring_a_bidirectional_one_to_many_relationship()
    {
        $relationships = Wired::together(
            Wire::it(
                From::the('member', Identified::by('member_id')),
                To::the('group', Identified::by('group_id')),
                HasOne::in('group')
            ),
            Wire::it(
                From::the('group', Identified::by('group_id')),
                To::the('member', Identified::by('member_id')),
                HasMany::in('members')
            )
        );

        $default = new Group('Default');
        $vip  = new Group('VIP');

        $john = new Member('John Doe');
        $foo = new Member('Foo Bar');
        $jackie = new Member('Jackie Chan');
        $chuck = new Member('Chuck Norris');

        $data = [
            [
                'group_id' => 1,
                'group_name' => 'Default',
                'member_id' => 'john',
                'member_name' => 'John Doe'
            ],
            [
                'group_id' => 1,
                'group_name' => 'Default',
                'member_id' => 'foo',
                'member_name' => 'Foo Bar'
            ],
            [
                'group_id' => 2,
                'group_name' => 'VIP',
                'member_id' => 'jackie',
                'member_name' => 'Jackie Chan'
            ],
            [
                'group_id' => 2,
                'group_name' => 'VIP',
                'member_id' => 'chuck',
                'member_name' => 'Chuck Norris'
            ],
        ];
        $objects = Result::fromArray([
            'member' => [
                'john' => $john,
                'foo' => $foo,
                'jackie' => $jackie,
                'chuck' => $chuck,
            ],
            'group' => [
                '1' => $default,
                '2' => $vip,
            ],
        ]);

        $relationships->wire($objects, $data);

        $this->assertSame($default, $john->group());
        $this->assertSame($default, $foo->group());
        $this->assertSame($vip, $jackie->group());
        $this->assertSame($vip, $chuck->group());

        $this->assertSame([$john, $foo], $default->members());
        $this->assertSame([$jackie, $chuck], $vip->members());
    }
}
