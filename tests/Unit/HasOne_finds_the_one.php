<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\TableLoader\From;
use Stratadox\TableLoader\HasOne;
use Stratadox\TableLoader\Identified;
use Stratadox\TableLoader\Test\Unit\Fixture\Group;
use Stratadox\TableLoader\Test\Unit\Fixture\Member;
use Stratadox\TableLoader\To;

/**
 * @covers \Stratadox\TableLoader\HasOne
 */
class HasOne_finds_the_one extends TestCase
{
    /** @test */
    function finding_the_related_object()
    {
        $relation = HasOne::in('group');

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

        $default = new Group('Default');
        $vip  = new Group('VIP');
        $objects = [
            'member' => [
                '#john' => new Member('John Doe'),
                '#foo' => new Member('Foo Bar'),
                '#jackie' => new Member('Jackie Chan'),
                '#chuck' => new Member('Chuck Norris'),
            ],
            'group' => [
                '#1' => $default,
                '#2' => $vip,
            ],
        ];

        $groupOf = $relation->load(
            From::the('member', Identified::by('member_id')),
            $data,
            To::the('group', Identified::by('group_id')),
            $objects
        )['group'];

        $this->assertSame($default, $groupOf['#john']);
        $this->assertSame($default, $groupOf['#foo']);
        $this->assertSame($vip, $groupOf['#jackie']);
        $this->assertSame($vip, $groupOf['#chuck']);
    }
}
