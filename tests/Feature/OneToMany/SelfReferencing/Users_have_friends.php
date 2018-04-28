<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\OneToMany\SelfReferencing;

use function assert;
use PHPUnit\Framework\TestCase;
use Stratadox\Hydration\Mapper\Instruction\Is;
use Stratadox\TableLoader\Joined;
use Stratadox\TableLoader\Load;
use Stratadox\TableLoader\LoadsTables;
use Stratadox\TableLoader\Test\Feature\OneToMany\SelfReferencing\Fixture\User;
use Stratadox\TableLoader\Test\Helper\TableTransforming;

/**
 * @coversNothing
 */
class Users_have_friends extends TestCase
{
    use TableTransforming;

    /** @test */
    function loading_users_and_friends_from_a_self_referencing_joined_table_result()
    {
        $data = $this->table([
            //---------+-------------+------------+
            [ 'user_id', 'user_name' , 'friend_id'],
            //---------+-------------+------------+
            [  1       , 'Alice'     ,  2         ],
            [  1       , 'Alice'     ,  3         ],
            [  2       , 'Bob'       ,  1         ],
            [  3       , 'Charlie'   ,  1         ],
            [  3       , 'Charlie'   ,  2         ],
            [  4       , 'Dan'       ,  5         ],
            [  5       , 'Eve'       ,  2         ],
            [  5       , 'Eve'       ,  4         ],
            //---------+-------------+------------+,
        ]);

        $make = Joined::table(
            Load::each('user')
                ->as(User::class, ['name' => Is::string()])
                ->havingMany('friends', 'friend'),
            Load::each('friend')->as(User::class)
        )();

        assert($make instanceof LoadsTables);

        /** @var User[] $users */
        $users = $make->from($data)['user'];

        $alice = $users['1'];
        $bob = $users['2'];
        $charlie = $users['3'];
        $dan = $users['4'];
        $eve = $users['5'];

        $this->assertTrue($alice->isFriendOf($bob));
        $this->assertTrue($alice->isFriendOf($charlie));
        $this->assertFalse($alice->isFriendOf($dan));
        $this->assertFalse($alice->isFriendOf($eve));

        $this->assertTrue($bob->isFriendOf($alice));
        $this->assertFalse($bob->isFriendOf($charlie));
        $this->assertFalse($bob->isFriendOf($dan));
        $this->assertFalse($bob->isFriendOf($eve));

        $this->assertTrue($charlie->isFriendOf($alice));
        $this->assertTrue($charlie->isFriendOf($bob));
        $this->assertFalse($charlie->isFriendOf($dan));
        $this->assertFalse($charlie->isFriendOf($eve));

        $this->assertFalse($dan->isFriendOf($alice));
        $this->assertFalse($dan->isFriendOf($bob));
        $this->assertFalse($dan->isFriendOf($charlie));
        $this->assertTrue($dan->isFriendOf($eve));

        $this->assertFalse($eve->isFriendOf($alice));
        $this->assertTrue($eve->isFriendOf($bob));
        $this->assertFalse($eve->isFriendOf($charlie));
        $this->assertTrue($eve->isFriendOf($dan));
    }
}
