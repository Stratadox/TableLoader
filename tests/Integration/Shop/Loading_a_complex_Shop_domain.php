<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop;

use function assert;
use PHPUnit\Framework\TestCase;
use Stratadox\IdentityMap\IdentityMap;
use Stratadox\TableLoader\Loader\ContainsResultingObjects;
use Stratadox\TableLoader\Test\Helper\TableTransforming;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Price\Money;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Product;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason\Feature;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason\NumberAttribute;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason\TextAttribute;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason\TextListAttribute;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Service;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\User\Customer;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\User\User;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\User\Username;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Infrastructure\MappedLoader;
use const PHP_EOL;

/**
 * @coversNothing
 */
class Loading_a_complex_Shop_domain extends TestCase
{
    use TableTransforming;

    private $alice;
    private $map;

    protected function setUp(): void
    {
        parent::setUp();
        $this->alice = new Customer(new Username('Alice'));
        $this->map = IdentityMap::with([
            '1' => $this->alice,
        ]);
    }

    /** @test */
    function loading_products_and_services_with_their_prices_features_and_attributes_eagerly_while_lazily_loading_reviews()
    {
        // @todo Actually load this from a sqlite db?
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
[ 1       , 'product' ,'TV set 1' , null              , 'USD'     , 36999        , 3         , 'text'      , 'Display'   , null       , 'LED'         ],
[ 1       , 'product' ,'TV set 1' , null              , 'USD'     , 36999        , 4         , 'text-list' , 'USB'       , null       , '1x USB3'     ],
[ 1       , 'product' ,'TV set 1' , null              , 'USD'     , 36999        , 4         , 'text-list' , 'USB'       , null       , '2x USB2'     ],
[ 1       , 'product' ,'TV set 1' , null              , 'EUR'     , 34999        , 1         , 'feature'   , 'Ultra HD'  , null       , null          ],
[ 1       , 'product' ,'TV set 1' , null              , 'EUR'     , 34999        , 2         , 'numeric'   , 'Diameter'  , 50         , null          ],
[ 1       , 'product' ,'TV set 1' , null              , 'EUR'     , 34999        , 3         , 'text'      , 'Display'   , null       , 'LED'         ],
[ 1       , 'product' ,'TV set 1' , null              , 'EUR'     , 34999        , 4         , 'text-list' , 'USB'       , null       , '1x USB3'     ],
[ 1       , 'product' ,'TV set 1' , null              , 'EUR'     , 34999        , 4         , 'text-list' , 'USB'       , null       , '2x USB2'     ],
[ 2       , 'product' ,'TV set 2' , null              , 'USD'     , 26999        , 5         , 'feature'   , 'Full HD'   , null       , null          ],
[ 2       , 'product' ,'TV set 2' , null              , 'USD'     , 26999        , 6         , 'text'      , 'Display'   , null       , 'LED'         ],
[ 2       , 'product' ,'TV set 2' , null              , 'USD'     , 26999        , 7         , 'numeric'   , 'Diameter'  , 47         , null          ],
[ 2       , 'product' ,'TV set 2' , null              , 'EUR'     , 25499        , 5         , 'feature'   , 'Full HD'   , null       , null          ],
[ 2       , 'product' ,'TV set 2' , null              , 'EUR'     , 25499        , 6         , 'text'      , 'Display'   , null       , 'LED'         ],
[ 2       , 'product' ,'TV set 2' , null              , 'EUR'     , 25499        , 7         , 'numeric'   , 'Diameter'  , 47         , null          ],
//--------+-----------+-----------+-------------------+-----------+--------------+-----------+-------------+-------------+------------+---------------+,
        ]);

        $reviewData = [
            Service::class => [
                '1' => $this->table([
                    ['review_id','review_rating','review_summary','review_full_text' ,'user_id','user_type','user_name','user_last_name'],
                    [ 2         , 1             ,'Took 2 days! ' ,'Took 2 days! WTF!', 2       ,'customer' ,'Bob'      , null           ],
                    [ 4         , 4             ,'Right on time' ,'Right on time :D' , 3       ,'customer' ,'Charlie'  , 'Chaplin'      ],
                ]),
                '2' => $this->table([
                    ['review_id','review_rating','review_summary','review_full_text'  ,'user_id','user_type','user_name','user_last_name'],
                    [ 1         , 5             ,'Got here fine' ,'Got here fine! :)' , 1       ,'customer' ,'Alice'    , null           ],
                ]),
            ],
            Product::class => [
                '1' => $this->table([
                    ['review_id','review_rating','review_summary','review_full_text'  ,'user_id','user_type','user_name','user_last_name'],
                    [ 3         , 3             ,'Not so bad.'   ,'Not so bad.'       , 2       ,'customer' ,'Bob'      , null           ],
                    [ 5         , 5             ,'Great colours' ,'Great colours!! :)', 1       ,'customer' ,'Alice'    , null           ],
                ]),
                '2' => $this->table([
                    ['review_id','review_rating','review_summary','review_full_text'  ,'user_id','user_type','user_name','user_last_name'],
                ]),
            ],
        ];

        $result = MappedLoader::withReviewData($reviewData)->from($data, $this->map);

        $sameDayDelivery = $result->get(Service::class, '1');
        $fourDayDelivery = $result->get(Service::class, '2');
        $tvSet1 = $result->get(Product::class, '1');
        $tvSet2 = $result->get(Product::class, '2');

        assert($sameDayDelivery instanceof Service);
        assert($fourDayDelivery instanceof Service);
        assert($tvSet1 instanceof Product);
        assert($tvSet2 instanceof Product);

        $this->checkDeliveryPrices($sameDayDelivery, $fourDayDelivery);
        $this->checkProductPrices($tvSet1, $tvSet2);
        $this->checkReasonsToBuy($tvSet1, $tvSet2);
        [$alice, $bob] = $this->checkDeliveryReviews($sameDayDelivery, $fourDayDelivery);
        $this->checkProductReviews($tvSet1, $tvSet2, $alice, $bob);
        $this->checkIdentityMap($result);
    }

    private function checkDeliveryPrices(Service $sameDayDelivery, Service $fourDayDelivery): void
    {
        $this->assertEquals(new Money(450, 'USD'), $sameDayDelivery->priceIn('USD'));
        $this->assertEquals('USD 4.50', $sameDayDelivery->priceIn('USD'));
        $this->assertEquals(new Money(375, 'EUR'), $sameDayDelivery->priceIn('EUR'));
        $this->assertEquals('EUR 3.75', $sameDayDelivery->priceIn('EUR'));

        $this->assertEquals(new Money(250, 'USD'), $fourDayDelivery->priceIn('USD'));
        $this->assertEquals('USD 2.50', $fourDayDelivery->priceIn('USD'));
        $this->assertEquals(new Money(175, 'EUR'), $fourDayDelivery->priceIn('EUR'));
        $this->assertEquals('EUR 1.75', $fourDayDelivery->priceIn('EUR'));
    }

    private function checkProductPrices(Product $tvSet1, Product $tvSet2): void
    {
        $this->assertEquals(new Money(36999, 'USD'), $tvSet1->priceIn('USD'));
        $this->assertEquals('USD 369.99', $tvSet1->priceIn('USD'));
        $this->assertEquals(new Money(34999, 'EUR'), $tvSet1->priceIn('EUR'));
        $this->assertEquals('EUR 349.99', $tvSet1->priceIn('EUR'));

        $this->assertEquals(new Money(26999, 'USD'), $tvSet2->priceIn('USD'));
        $this->assertEquals('USD 269.99', $tvSet2->priceIn('USD'));
        $this->assertEquals(new Money(25499, 'EUR'), $tvSet2->priceIn('EUR'));
        $this->assertEquals('EUR 254.99', $tvSet2->priceIn('EUR'));
    }

    private function checkReasonsToBuy(Product $tvSet1, Product $tvSet2): void
    {
        // Features of TV Set 1
        $this->assertEquals('Ultra HD', $tvSet1->features());
        $this->assertEquals('Ultra HD', $tvSet1->features()[0]);
        $this->assertCount(1, $tvSet1->features());

        // Attributes of TV Set 1
        $this->assertEquals(
            'Diameter: 50' . PHP_EOL . 'Display: LED' . PHP_EOL . 'USB: 1x USB3, 2x USB2',
            $tvSet1->attributes()
        );

        $this->assertCount(3, $tvSet1->attributes());

        /** @var NumberAttribute $diameter */
        $diameter = $tvSet1->attributes()[0];
        $this->assertEquals('Diameter: 50', $diameter);
        $this->assertSame('Diameter', $diameter->name());
        $this->assertSame(50, $diameter->value());

        /** @var TextAttribute $display */
        $display = $tvSet1->attributes()[1];
        $this->assertEquals('Display: LED', $display);
        $this->assertSame('Display', $display->name());
        $this->assertEquals('LED', $display->value());

        /** @var TextListAttribute $usb */
        $usb = $tvSet1->attributes()[2];
        $this->assertEquals('USB: 1x USB3, 2x USB2', $usb);
        $this->assertSame('USB', $usb->name());
        $this->assertEquals(['1x USB3', '2x USB2'], $usb->value());

        // Features of TV Set 2
        $this->assertEquals('Full HD', $tvSet2->features());
        $this->assertEquals('Full HD', $tvSet2->features()[0]);
        $this->assertCount(1, $tvSet2->features());

        // Attributes of TV Set 2
        $this->assertEquals(
            'Display: LED' . PHP_EOL .
            'Diameter: 47',
            $tvSet2->attributes()
        );

        $this->assertCount(2, $tvSet2->attributes());

        /** @var TextAttribute $display */
        $display = $tvSet2->attributes()[0];
        $this->assertEquals('Display: LED', $display);
        $this->assertSame('Display', $display->name());
        $this->assertEquals('LED', $display->value());

        /** @var NumberAttribute $diameter */
        $diameter = $tvSet2->attributes()[1];
        $this->assertEquals('Diameter: 47', $diameter);
        $this->assertSame('Diameter', $diameter->name());
        $this->assertSame(47, $diameter->value());
    }

    /** @return User[] */
    private function checkDeliveryReviews(Service $sameDayDelivery, Service $fourDayDelivery): array
    {
        $this->assertCount(2, $sameDayDelivery->reviews());
        $this->assertCount(1, $fourDayDelivery->reviews());

        // Alice's review of Four Day delivery

        $this->assertSame('Got here fine', $fourDayDelivery->reviews()[0]->opinion()->summary());
        $this->assertSame('Got here fine! :)', $fourDayDelivery->reviews()[0]->opinion()->fullText());

        $this->assertSame($this->alice, $fourDayDelivery->reviews()[0]->author());

        // Bobs review of Same Day delivery

        $this->assertSame('Took 2 days! WTF!', $sameDayDelivery->reviews()[0]->opinion()->fullText());

        $this->assertEquals('1 / 5', $sameDayDelivery->reviews()[0]->rating());
        $this->assertSame(1, $sameDayDelivery->reviews()[0]->rating()->score());

        $bob = $sameDayDelivery->reviews()[0]->author();
        $this->assertEquals('Bob', $bob);
        $this->assertEquals('Bob', $bob->name());
        $this->assertEquals('Bob', $bob->name()->firstName());
        $this->assertNull($bob->name()->lastName());

        // Charlies review of Same Day delivery

        $this->assertSame('Right on time :D', $sameDayDelivery->reviews()[1]->opinion()->fullText());
        $this->assertSame('Right on time', $sameDayDelivery->reviews()[1]->opinion()->summary());

        $this->assertEquals('4 / 5', $sameDayDelivery->reviews()[1]->rating());
        $this->assertSame(4, $sameDayDelivery->reviews()[1]->rating()->score());

        $charlie = $sameDayDelivery->reviews()[1]->author();
        $this->assertEquals('Charlie Chaplin', $charlie);
        $this->assertEquals('Charlie Chaplin', $charlie->name());
        $this->assertEquals('Charlie', $charlie->name()->firstName());
        $this->assertEquals('Chaplin', $charlie->name()->lastName());

        return [$this->alice, $bob, $charlie];
    }

    private function checkProductReviews(
        Product $tvSet1,
        Product $tvSet2,
        User $alice,
        User $bob
    ): void {
        $this->assertCount(2, $tvSet1->reviews());

        $this->assertSame('Not so bad.', $tvSet1->reviews()[0]->opinion()->summary());
        $this->assertSame('Not so bad.', $tvSet1->reviews()[0]->opinion()->fullText());

        $this->assertSame('Great colours', $tvSet1->reviews()[1]->opinion()->summary());
        $this->assertSame('Great colours!! :)', $tvSet1->reviews()[1]->opinion()->fullText());

        $this->assertSame($bob, $tvSet1->reviews()[0]->author());
        $this->assertSame($alice, $tvSet1->reviews()[1]->author());

        $this->assertEmpty($tvSet2->reviews());
    }

    private function checkIdentityMap(ContainsResultingObjects $result): void
    {
        $this->assertTrue($result->has(Product::class, '1'));
        $this->assertTrue($result->has(Product::class, '2'));
        $this->assertFalse($result->has(Product::class, '3'));

        $this->assertTrue($result->has(Service::class, '1'));
        $this->assertTrue($result->has(Service::class, '2'));
        $this->assertFalse($result->has(Service::class, '3'));

        // Ignored "identities"
        $this->assertFalse($result->has(Money::class, 'USD:450'));
        $this->assertFalse($result->has(Feature::class, '1'));
        $this->assertFalse($result->has(NumberAttribute::class, '2'));
        $this->assertFalse($result->has(TextAttribute::class, '3'));
        $this->assertFalse($result->has(TextListAttribute::class, '4'));
        $this->assertFalse($result->has(Feature::class, '5'));
        $this->assertFalse($result->has(TextAttribute::class, '6'));
        $this->assertFalse($result->has(NumberAttribute::class, '7'));
    }
}
