<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Loader;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydrator\VariadicConstructor;
use Stratadox\TableLoader\Loader\From;
use Stratadox\TableLoader\Loader\HasMany;
use Stratadox\TableLoader\Loader\HasOne;
use Stratadox\TableLoader\Loader\Identified;
use Stratadox\TableLoader\Loader\Result;
use Stratadox\TableLoader\Test\Unit\Fixture\Child;
use Stratadox\TableLoader\Test\Unit\Fixture\Group;
use Stratadox\TableLoader\Test\Unit\Fixture\Member;
use Stratadox\TableLoader\Test\Unit\Fixture\OtherChild;
use Stratadox\TableLoader\Test\Unit\Fixture\Thing;
use Stratadox\TableLoader\Test\Unit\Fixture\Things;
use Stratadox\TableLoader\Loader\To;
use Stratadox\TableLoader\Loader\Wire;
use Stratadox\TableLoader\Loader\Wired;

/**
 * @covers \Stratadox\TableLoader\Loader\Wired
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

    /** @test */
    function wiring_a_one_to_one_or_one_to_many_based_on_the_subclass()
    {
        $relationships = Wired::together(
            Wire::it(
                From::onlyThe(Child::class, 'kid', Identified::by('kid_name')),
                To::the('toy', Identified::by('toy_id')),
                HasOne::in('toy')
            ),
            Wire::it(
                From::onlyThe(OtherChild::class, 'kid', Identified::by('kid_name')),
                To::the('toy', Identified::by('toy_id')),
                HasMany::in('toys', VariadicConstructor::forThe(Things::class))
            )
        );

        $data = [
            ['kid_name' => 'Kid 1', 'kid_type' => 'child', 'toy_id' => 1, 'toy_name' => 'Toy 1'],
            ['kid_name' => 'Kid 2', 'kid_type' => 'other', 'toy_id' => 2, 'toy_name' => 'Toy 2'],
            ['kid_name' => 'Kid 2', 'kid_type' => 'other', 'toy_id' => 3, 'toy_name' => 'Toy 3'],
        ];
        $toy1 = new Thing(1, 'Toy 1');
        $toy2 = new Thing(2, 'Toy 2');
        $toy3 = new Thing(3, 'Toy 3');
        $kid1 = new Child('Kid 1');
        $kid2 = new OtherChild('Kid 1');

        $objects = Result::fromArray([
            'kid' => [
                'Kid 1' => $kid1,
                'Kid 2' => $kid2,
            ],
            'toy' => [
                '1' => $toy1,
                '2' => $toy2,
                '3' => $toy3,
            ],
        ]);

        $relationships->wire($objects, $data);

        $this->assertSame($toy1, $kid1->toy());
        $this->assertSame($toy2, $kid2->toys()[0]);
        $this->assertSame($toy3, $kid2->toys()[1]);
    }
}
