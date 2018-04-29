<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\AllCombined;

use function assert;
use const PHP_EOL;
use PHPUnit\Framework\TestCase;
use Stratadox\Hydration\Mapper\Instruction\Has;
use Stratadox\Hydration\Mapper\Instruction\In;
use Stratadox\Hydration\Mapper\Instruction\Is;
use Stratadox\TableLoader\Decide;
use Stratadox\TableLoader\InCase;
use Stratadox\TableLoader\Joined;
use Stratadox\TableLoader\Load;
use Stratadox\TableLoader\LoadsTables;
use Stratadox\TableLoader\Test\Feature\AllCombined\Fixture\Feature;
use Stratadox\TableLoader\Test\Feature\AllCombined\Fixture\IntValue;
use Stratadox\TableLoader\Test\Feature\AllCombined\Fixture\Money;
use Stratadox\TableLoader\Test\Feature\AllCombined\Fixture\NumberAttribute;
use Stratadox\TableLoader\Test\Feature\AllCombined\Fixture\Prices;
use Stratadox\TableLoader\Test\Feature\AllCombined\Fixture\Product;
use Stratadox\TableLoader\Test\Feature\AllCombined\Fixture\ReasonsToBuy;
use Stratadox\TableLoader\Test\Feature\AllCombined\Fixture\Service;
use Stratadox\TableLoader\Test\Feature\AllCombined\Fixture\TextAttribute;
use Stratadox\TableLoader\Test\Feature\AllCombined\Fixture\TextListAttribute;
use Stratadox\TableLoader\Test\Feature\AllCombined\Fixture\TextValue;
use Stratadox\TableLoader\Test\Helper\TableTransforming;

/**
 * @coversNothing
 */
class Loading_a_complex_Shop_domain extends TestCase
{
    use TableTransforming;

    /** @test */
    function loading_products_and_services_with_their_prices_features_and_attributes()
    {
        $data = $this->table([
//--------+-----------+-----------+-------------------+-----------+--------------+-----------+-------------+-------------+------------+---------------+,
['item_id','item_type','item_name','item_text'        ,'price_iso','price_amount','reason_id','reason_type','reason_name','reason_int','reason_x_text'],
//--------+-----------+-----------+-------------------+-----------+--------------+-----------+-------------+-------------+------------+---------------+,
[ 1       , 'service' ,'Delivery1','Same day shipping', 'USD'     , 450          , null      , null        , null        , null       , null          ],
[ 1       , 'service' ,'Delivery1','Same day shipping', 'EUR'     , 375          , null      , null        , null        , null       , null          ],
[ 2       , 'service' ,'Delivery4','Four day shipping', 'USD'     , 250          , null      , null        , null        , null       , null          ],
[ 2       , 'service' ,'Delivery4','Four day shipping', 'EUR'     , 175          , null      , null        , null        , null       , null          ],
[ 1       , 'product' ,'TV set 1' , null              , 'USD'     , 36999        , 1         , 'feature'   , 'Ultra HD'  , null       , null          ],
[ 1       , 'product' ,'TV set 1' , null              , 'USD'     , 36999        , 2         , 'numeric'   , 'Diameter'  , 50         , null          ],
[ 1       , 'product' ,'TV set 1' , null              , 'USD'     , 36999        , 2         , 'text'      , 'Display'   , null       , 'LED'         ],
[ 1       , 'product' ,'TV set 1' , null              , 'USD'     , 36999        , 3         , 'text-list' , 'USB'       , null       , '1x USB3'     ],
[ 1       , 'product' ,'TV set 1' , null              , 'USD'     , 36999        , 3         , 'text-list' , 'USB'       , null       , '2x USB2'     ],
[ 1       , 'product' ,'TV set 1' , null              , 'EUR'     , 34999        , 1         , 'feature'   , 'Ultra HD'  , null       , null          ],
[ 1       , 'product' ,'TV set 1' , null              , 'EUR'     , 34999        , 2         , 'numeric'   , 'Diameter'  , 50         , null          ],
[ 1       , 'product' ,'TV set 1' , null              , 'EUR'     , 34999        , 2         , 'text'      , 'Display'   , null       , 'LED'         ],
[ 1       , 'product' ,'TV set 1' , null              , 'EUR'     , 34999        , 3         , 'text-list' , 'USB'       , null       , '1x USB3'     ],
[ 1       , 'product' ,'TV set 1' , null              , 'EUR'     , 34999        , 3         , 'text-list' , 'USB'       , null       , '2x USB2'     ],
[ 2       , 'product' ,'TV set 2' , null              , 'USD'     , 26999        , 4         , 'feature'   , 'Full HD'   , null       , null          ],
[ 2       , 'product' ,'TV set 2' , null              , 'USD'     , 26999        , 5         , 'text'      , 'Display'   , null       , 'LED'         ],
[ 2       , 'product' ,'TV set 2' , null              , 'USD'     , 26999        , 6         , 'numeric'   , 'Diameter'  , 47         , null          ],
[ 2       , 'product' ,'TV set 2' , null              , 'EUR'     , 25499        , 4         , 'feature'   , 'Full HD'   , null       , null          ],
[ 2       , 'product' ,'TV set 2' , null              , 'EUR'     , 25499        , 5         , 'text'      , 'Display'   , null       , 'LED'         ],
[ 2       , 'product' ,'TV set 2' , null              , 'EUR'     , 25499        , 6         , 'numeric'   , 'Diameter'  , 47         , null          ],
//--------+-----------+-----------+-------------------+-----------+--------------+-----------+-------------+-------------+------------+---------------+,
        ]);

        $make = Joined::table(
            Decide::which('item')->basedOn('type', ...[
                InCase::of('service')->as(Service::class, ['description' => In::key('text')]),
                InCase::of('product')->as(Product::class)->havingMany('reasonsToBuy', 'reason', ReasonsToBuy::class)
            ])->with(['name' => Is::string()])->havingMany('prices', 'price', Prices::class),

            Load::each('price')->as(Money::class, [
                'currency' => In::key('iso'),
                'amount' => Is::int()
            ])->by('iso', 'amount'),

            Decide::which('reason')->basedOn('type', ...[
                InCase::of('feature')->as(Feature::class),
                InCase::of('numeric')->as(NumberAttribute::class, [
                    'value' => Has::one(IntValue::class)->with('value', Is::intInKey('int'))
                ]),
                InCase::of('text')->as(TextAttribute::class, [
                    'value' => Has::one(TextValue::class)->with('value', Is::stringInKey('x_text'))
                ]),
                InCase::of('text-list')->as(TextListAttribute::class)->havingMany('value', 'reason_x'),
            ])->with(['name' => Is::string()]),
            Load::each('reason_x')
                ->by('text')
                ->as(TextValue::class, ['value' => Is::stringInKey('text')])
        )();

        assert($make instanceof LoadsTables);

        $catalogue = $make->from($data)['item'];

        $sameDayDelivery = $catalogue['service:1'];
        $fourDayDelivery = $catalogue['service:2'];
        $tvSet1 = $catalogue['product:1'];
        $tvSet2 = $catalogue['product:2'];

        assert($sameDayDelivery instanceof Service);
        assert($fourDayDelivery instanceof Service);
        assert($tvSet1 instanceof Product);
        assert($tvSet2 instanceof Product);

        $this->assertEquals(new Money(450, 'USD'), $sameDayDelivery->priceIn('USD'));
        $this->assertEquals(new Money(375, 'EUR'), $sameDayDelivery->priceIn('EUR'));
        $this->assertEquals(new Money(250, 'USD'), $fourDayDelivery->priceIn('USD'));
        $this->assertEquals(new Money(175, 'EUR'), $fourDayDelivery->priceIn('EUR'));

        $this->assertEquals('USD 2.50', $fourDayDelivery->priceIn('USD'));
        $this->assertEquals('EUR 1.75', $fourDayDelivery->priceIn('EUR'));

        $this->assertEquals('Ultra HD', $tvSet1->features());
        $this->assertEquals(
            'Diameter: 50' . PHP_EOL .
            'Display: LED' . PHP_EOL .
            'USB: 1x USB3, 2x USB2',
            $tvSet1->attributes()
        );
        $this->assertEquals('USD 369.99', $tvSet1->priceIn('USD'));
        $this->assertEquals('EUR 349.99', $tvSet1->priceIn('EUR'));

        $this->assertEquals('Full HD', $tvSet2->features());
        $this->assertEquals(
            'Display: LED' . PHP_EOL .
            'Diameter: 47',
            $tvSet2->attributes()
        );
        $this->assertEquals('USD 269.99', $tvSet2->priceIn('USD'));
        $this->assertEquals('EUR 254.99', $tvSet2->priceIn('EUR'));
    }
}
