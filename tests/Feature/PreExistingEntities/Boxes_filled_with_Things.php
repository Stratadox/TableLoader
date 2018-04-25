<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\PreExistingEntities;

use PHPUnit\Framework\TestCase;
use Stratadox\IdentityMap\IdentityMap;
use Stratadox\TableLoader\Joined;
use Stratadox\TableLoader\Load;
use Stratadox\TableLoader\LoadsTable;
use Stratadox\TableLoader\Test\Feature\PreExistingEntities\Fixture\Box;
use Stratadox\TableLoader\Test\Feature\PreExistingEntities\Fixture\BoxProxy;
use Stratadox\TableLoader\Test\Feature\PreExistingEntities\Fixture\Thing;
use Stratadox\TableLoader\Test\Helper\TableTransforming;

/**
 * @coversNothing
 */
class Boxes_filled_with_Things extends TestCase
{
    use TableTransforming;

    /** @test */
    function loading_boxes_with_their_things_using_previously_loaded_entities()
    {
        $foo = new Thing('Foo', new BoxProxy);
        $bar = new Thing('Bar', new BoxProxy);
        $identityMap = IdentityMap::with(['#Foo' => $foo]);

        $table = $this->table([
            //--------+--------------+,
            [ 'box_id', 'thing_name' ],
            //--------+--------------+,
            [  1      , 'Foo'        ],
            [  1      , 'Bar'        ],
            //--------+--------------+,
        ]);

        /** @var LoadsTable $make */
        $make = Joined::table(
            Load::each('box')
                ->by('id')
                ->as(Box::class, [])
                ->havingMany('items', 'thing'),
            Load::each('thing')
                ->by('name')
                ->as(Thing::class)
                ->havingOne('box', 'box')
        )();

        $result = $make->from($table, $identityMap);
        $identityMap = $result->identityMap();

        $this->assertSame(
            $foo,
            $result['thing']['#Foo'],
            'Foo should be reused because it was in the identity map.'
        );
        $this->assertNotSame(
            $bar,
            $result['thing']['#Bar'],
            'Bar should not be reused because it was not in the identity map.'
        );
        $this->assertNotInstanceOf(
            BoxProxy::class,
            $foo->box(),
            'The box relation of Foo should be updated.'
        );
        $this->assertTrue(
            $identityMap->has(Thing::class, '#Bar'),
            'Bar should now be in the identity map.'
        );
    }
}
