<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop;

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
use Stratadox\TableLoader\Test\Integration\Shop\Fixture\Admin;
use Stratadox\TableLoader\Test\Integration\Shop\Fixture\Customer;
use Stratadox\TableLoader\Test\Integration\Shop\Fixture\Feature;
use Stratadox\TableLoader\Test\Integration\Shop\Fixture\NumberValue;
use Stratadox\TableLoader\Test\Integration\Shop\Fixture\Money;
use Stratadox\TableLoader\Test\Integration\Shop\Fixture\NumberAttribute;
use Stratadox\TableLoader\Test\Integration\Shop\Fixture\Opinion;
use Stratadox\TableLoader\Test\Integration\Shop\Fixture\Prices;
use Stratadox\TableLoader\Test\Integration\Shop\Fixture\Product;
use Stratadox\TableLoader\Test\Integration\Shop\Fixture\Rating;
use Stratadox\TableLoader\Test\Integration\Shop\Fixture\ReasonsToBuy;
use Stratadox\TableLoader\Test\Integration\Shop\Fixture\Infrastructure\ReviewsLoaderFactory;
use Stratadox\TableLoader\Test\Integration\Shop\Fixture\Infrastructure\ReviewsProxy;
use Stratadox\TableLoader\Test\Integration\Shop\Fixture\Review;
use Stratadox\TableLoader\Test\Integration\Shop\Fixture\Service;
use Stratadox\TableLoader\Test\Integration\Shop\Fixture\TextAttribute;
use Stratadox\TableLoader\Test\Integration\Shop\Fixture\TextListAttribute;
use Stratadox\TableLoader\Test\Integration\Shop\Fixture\TextValue;
use Stratadox\TableLoader\Test\Integration\Shop\Fixture\User;
use Stratadox\TableLoader\Test\Integration\Shop\Fixture\Username;
use Stratadox\TableLoader\Test\Helper\TableTransforming;

/**
 * @coversNothing
 */
class Loading_a_complex_Shop_domain extends TestCase
{
    use TableTransforming;

    /** @test */
    function loading_products_and_services_with_their_prices_features_and_attributes_eagerly_while_lazily_loading_reviews()
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
            ],
        ];

        /**
         * @var LoadsTables          $make
         * @var ReviewsLoaderFactory $reviewLoader
         */
        [$make, $reviewLoader] = $this->tableLoader($reviewData);

        $result = $make->from($data);

        $reviewLoader->setIdentityMap($result->identityMap());
        $catalogue = $result['item'];

        $sameDayDelivery = $catalogue['service:1'];
        $fourDayDelivery = $catalogue['service:2'];
        $tvSet1 = $catalogue['product:1'];
        $tvSet2 = $catalogue['product:2'];

        $this->checkDeliveryPrices($sameDayDelivery, $fourDayDelivery);
        $this->checkProductPrices($tvSet1, $tvSet2);
        $this->checkReasonsToBuy($tvSet1, $tvSet2);
        [$alice, $bob] = $this->checkDeliveryReviews($sameDayDelivery, $fourDayDelivery);
        $this->checkProductReviews($tvSet1, $tvSet2, $alice, $bob);
    }

    // Checks

    private function checkDeliveryPrices(Service $sameDayDelivery, Service $fourDayDelivery): void
    {
        $this->assertEquals(new Money(450, 'USD'), $sameDayDelivery->priceIn('USD'));
        $this->assertEquals(new Money(375, 'EUR'), $sameDayDelivery->priceIn('EUR'));
        $this->assertEquals(new Money(250, 'USD'), $fourDayDelivery->priceIn('USD'));
        $this->assertEquals(new Money(175, 'EUR'), $fourDayDelivery->priceIn('EUR'));

        $this->assertEquals('USD 4.50', $sameDayDelivery->priceIn('USD'));
        $this->assertEquals('EUR 3.75', $sameDayDelivery->priceIn('EUR'));
        $this->assertEquals('USD 2.50', $fourDayDelivery->priceIn('USD'));
        $this->assertEquals('EUR 1.75', $fourDayDelivery->priceIn('EUR'));
    }

    private function checkProductPrices(Product $tvSet1, Product $tvSet2): void
    {
        $this->assertEquals(new Money(36999, 'USD'), $tvSet1->priceIn('USD'));
        $this->assertEquals(new Money(34999, 'EUR'), $tvSet1->priceIn('EUR'));
        $this->assertEquals(new Money(26999, 'USD'), $tvSet2->priceIn('USD'));
        $this->assertEquals(new Money(25499, 'EUR'), $tvSet2->priceIn('EUR'));

        $this->assertEquals('USD 369.99', $tvSet1->priceIn('USD'));
        $this->assertEquals('EUR 349.99', $tvSet1->priceIn('EUR'));
        $this->assertEquals('USD 269.99', $tvSet2->priceIn('USD'));
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

        $alice = $fourDayDelivery->reviews()[0]->author();
        $this->assertEquals('Alice', $alice);
        $this->assertEquals('Alice', $alice->name());
        $this->assertEquals('Alice', $alice->name()->firstName());
        $this->assertNull($alice->name()->lastName());

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

        return [$alice, $bob, $charlie];
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

    // Mapping

    private function tableLoader(array $reviewData): array
    {
        $reviewLoader = $this->reviewLoader($reviewData);
        return [Joined::table(
            Decide::which('item')->basedOn('type', ...[
                InCase::of('service')
                    ->as(Service::class, [
                        'description' => Is::stringInKey('text')
                    ]),
                InCase::of('product')
                    ->as(Product::class)
                    ->havingMany('reasonsToBuy', 'reason', ReasonsToBuy::class)
            ])->with([
                'name' => Is::string(),
                'reviews' => Has::many(Review::class)
                    ->containedInA(ReviewsProxy::class)
                    ->loadedBy($reviewLoader)
            ])->havingMany('prices', 'price', Prices::class),

            Load::each('price')->as(Money::class, [
                'currency' => In::key('iso'),
                'amount' => Is::int(),
            ])->by('iso', 'amount'),

            Decide::which('reason')->basedOn('type', ...[
                InCase::of('feature')->as(Feature::class),
                InCase::of('numeric')->as(NumberAttribute::class, [
                    'value' => Has::one(NumberValue::class)->with('value', Is::intInKey('int'))
                ]),
                InCase::of('text')->as(TextAttribute::class, [
                    'value' => Has::one(TextValue::class)->with('value', Is::stringInKey('x_text'))
                ]),
                InCase::of('text-list')->as(TextListAttribute::class)->havingMany('value', 'reason_x'),
            ])->with(['name' => Is::string()]),
            Load::each('reason_x')
                ->by('text')
                ->as(TextValue::class, ['value' => Is::stringInKey('text')])
        )(), $reviewLoader];
    }

    private function reviewLoader(array $reviewData): ReviewsLoaderFactory
    {
        return ReviewsLoaderFactory::withThe(
            Joined::table(
                Load::each('review')
                    ->as(Review::class, [
                        'rating' => Has::one(Rating::class)
                            ->with('score', Is::intInKey('rating')),
                        'opinion' => Has::one(Opinion::class)
                            ->with('summary')
                            ->with('fullText', In::key('full_text'))
                    ])
                    ->havingOne('author', 'user'),
                Decide::which('user')->basedOn('type', ...[
                    InCase::of('customer')->as(Customer::class),
                    InCase::of('admin')->as(Admin::class),
                ])->with(['name' => Has::one(Username::class)
                    ->with('name', Is::string())
                    ->with('lastName', Is::stringInKey('last_name')->nullable())
                ])
            )(),
            $reviewData
        );
    }
}
