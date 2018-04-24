<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\ManyToOne;

use function assert;
use PHPUnit\Framework\TestCase;
use Stratadox\Hydration\Mapper\Instruction\Is;
use Stratadox\TableLoader\Joined;
use Stratadox\TableLoader\Load;
use Stratadox\TableLoader\LoadsTable;
use Stratadox\TableLoader\Test\Feature\ManyToOne\Fixture\OrderLine;
use Stratadox\TableLoader\Test\Feature\ManyToOne\Fixture\Product;
use Stratadox\TableLoader\Test\Helper\TableTransforming;

/**
 * @coversNothing
 */
class OrderLines_refer_to_Products extends TestCase
{
    use TableTransforming;

    /** @test */
    function loading_clubs_and_their_members_from_a_joined_table_result()
    {
        $data = $this->table([
            //------------+----------------+----------------+----------+-----------------+,
            [ 'product_id', 'product_name' , 'product_price', 'line_id', 'line_quantity' ],
            //------------+----------------+----------------+----------+-----------------+,
            [ 1           , 'Foo'          , 120            , 1        , 1               ],
            [ 1           , 'Foo'          , 120            , 2        , 3               ],
            [ 2           , 'Bar'          , 100            , 3        , 2               ],
            [ 3           , 'Qux'          , 135            , 4        , 4               ],
            [ 3           , 'Qux'          , 135            , 5        , 2               ],
            //------------+----------------+----------------+----------+-----------------+,
        ]);

        $make = Joined::table(
            Load::each('product')
                ->by('id')
                ->as(Product::class, [
                    'name' => Is::string(),
                    'price' => Is::int(),
                ]),
            Load::each('line')
                ->by('id')
                ->as(OrderLine::class, [
                    'quantity' => Is::int()
                ])
                ->havingOne('product', 'product')
        )();

        assert($make instanceof LoadsTable);

        /** @var OrderLine[] $orderLines */
        $orderLines = $make->from($data)['line'];

        $this->assertSame(120, $orderLines['#1']->totalPrice());
        $this->assertSame(360, $orderLines['#2']->totalPrice());
        $this->assertSame(200, $orderLines['#3']->totalPrice());
        $this->assertSame(540, $orderLines['#4']->totalPrice());
        $this->assertSame(270, $orderLines['#5']->totalPrice());

        $this->assertSame($orderLines['#1']->product(), $orderLines['#2']->product());
        $this->assertNotSame($orderLines['#2']->product(), $orderLines['#3']->product());
        $this->assertSame($orderLines['#4']->product(), $orderLines['#5']->product());

        $this->assertSame('Foo', (string) $orderLines['#1']->product());
        $this->assertSame('Bar', (string) $orderLines['#3']->product());
        $this->assertSame('Qux', (string) $orderLines['#5']->product());
    }
}
