<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\PreExistingEntities;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydration\Mapper\Instruction\Has;
use Stratadox\Hydration\Mapper\Instruction\Is;
use Stratadox\TableLoader\Builder\Joined;
use Stratadox\TableLoader\Builder\Load;
use Stratadox\TableLoader\Loader\LoadsTables;
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
        /** @var LoadsTables $make */
        $make = Joined::table(
            Load::each('thing')
                ->as(Thing::class, [
                    'name' => Is::string(),
                    'box' => Has::one(BoxProxy::class)
                ])
                ->by('name')
        )();

        $table = $this->table([
            //--------+--------------+,
            [ 'box_id', 'thing_name' ],
            //--------+--------------+,
            [  1      , 'Foo'        ],
            //--------+--------------+,
        ]);

        $previousResult = $make->from($table);

        /** @var Thing $foo */
        $foo = $previousResult['thing']['Foo'];
        $this->assertInstanceOf(
            BoxProxy::class,
            $foo->box(),
            'The box should be a proxy for now: we did not load all Things yet.'
        );

        /** @var LoadsTables $make */
        $make = Joined::table(
            Load::each('box')
                ->as(Box::class)
                ->by('id')
                ->havingMany('items', 'thing'),
            Load::each('thing')
                ->as(Thing::class)
                ->by('name')
                ->havingOne('box', 'box')
        )();

        $table = $this->table([
            //--------+--------------+,
            [ 'box_id', 'thing_name' ],
            //--------+--------------+,
            [  1      , 'Foo'        ],
            [  1      , 'Bar'        ],
            //--------+--------------+,
        ]);

        $result = $make->from($table, $previousResult->identityMap());
        $identityMap = $result->identityMap();

        $this->assertSame(
            $foo,
            $result['thing']['Foo'],
            'Foo should be reused because it was in the identity map.'
        );
        $this->assertNotInstanceOf(
            BoxProxy::class,
            $foo->box(),
            'The box should not be a proxy anymore, now that the real one is loaded.'
        );

        $this->assertTrue(
            $identityMap->has(Thing::class, 'Bar'),
            'Bar should now be in the identity map as well.'
        );
        $this->assertCount(2, $result['box'][1], 'Expecting two things in the box.');
    }
}
