<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\OneToOne\WithInheritance;

use function assert;
use PHPUnit\Framework\TestCase;
use Stratadox\Hydration\Mapper\Instruction\Is;
use Stratadox\TableLoader\Decide;
use Stratadox\TableLoader\InCase;
use Stratadox\TableLoader\Joined;
use Stratadox\TableLoader\LoadsTable;
use Stratadox\TableLoader\Test\Feature\OneToOne\WithInheritance\Fixture\Admin;
use Stratadox\TableLoader\Test\Feature\OneToOne\WithInheritance\Fixture\AdminProfile;
use Stratadox\TableLoader\Test\Feature\OneToOne\WithInheritance\Fixture\Customer;
use Stratadox\TableLoader\Test\Feature\OneToOne\WithInheritance\Fixture\RegularProfile;
use Stratadox\TableLoader\Test\Feature\OneToOne\WithInheritance\Fixture\VipProfile;
use Stratadox\TableLoader\Test\Helper\TableTransforming;

/**
 * @coversNothing
 */
class Users_have_a_profile extends TestCase
{
    use TableTransforming;

    /** @test */
    function loading_concrete_users_with_concrete_profiles()
    {
        $data = $this->table([
            //---------+------------+-------------+-------------+---------------+-----------------+-----------------+,
            [ 'user_id', 'user_type', 'user_name' , 'profile_id', 'profile_type', 'profile_about' , 'profile_level' ],
            //---------+------------+-------------+-------------+---------------+-----------------+-----------------+,
            [  1       , 'admin'    , 'Mr. Admin' ,  1          , 'admin'       , 'This is Admin.', 10              ],
            [  1       , 'customer' , 'Alice'     ,  2          , 'vip'         , 'Hello world!'  , null            ],
            [  2       , 'customer' , 'John Doe'  ,  3          , 'regular'     , 'Hi there!'     , null            ],
            //---------+------------+-------------+-------------+---------------+-----------------+-----------------+,
        ]);

        $make = Joined::table(
            Decide::which('user')->basedOn('type', ...[
                InCase::of('admin')
                    ->as(Admin::class)
                    ->havingOne('adminProfile', 'profile'),
                InCase::of('customer')
                    ->as(Customer::class)
                    ->havingOne('customerProfile', 'profile')
            ])->by('id')->with([
                'name' => Is::string()
            ]),
            Decide::which('profile')->basedOn('type', ...[
                InCase::of('admin')->as(AdminProfile::class, ['level' => Is::int()]),
                InCase::of('regular')->as(RegularProfile::class),
                InCase::of('vip')->as(VipProfile::class),
            ])->by('id')->with(['about' => Is::string()])
        )();

        assert($make instanceof LoadsTable);

        $result = $make->from($data);

        $admin = $result['users']['admin:1'];
        assert($admin instanceof Admin);
        assert($admin->name() === 'Mr. Admin');

        $adminProfile = $admin->profile();
        assert($adminProfile instanceof AdminProfile);
        assert($adminProfile->about() === 'Level 10 admin profile: This is Admin.');

        $alice = $result['users']['customer:1'];
        assert($alice instanceof Customer);
        assert($alice->name() === 'Alice');

        $aliceProfile = $alice->profile();
        assert($aliceProfile instanceof VipProfile);
        assert($aliceProfile->about() === 'VIP profile: Hello world!');

        $john = $result['users']['customer:2'];
        assert($john instanceof Customer);
        assert($john->name() === 'John Doe');

        $johnProfile = $john->profile();
        assert($johnProfile instanceof RegularProfile);
        assert($johnProfile->about() === 'Profile: Hi there!');
    }
}
