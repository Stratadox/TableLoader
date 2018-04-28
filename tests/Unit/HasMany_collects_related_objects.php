<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydrator\VariadicConstructor;
use Stratadox\TableLoader\CannotLoadTable;
use Stratadox\TableLoader\From;
use Stratadox\TableLoader\HasMany;
use Stratadox\TableLoader\Identified;
use Stratadox\TableLoader\Result;
use Stratadox\TableLoader\Test\Unit\Fixture\Bar;
use Stratadox\TableLoader\Test\Unit\Fixture\Basket;
use Stratadox\TableLoader\Test\Unit\Fixture\Customer;
use Stratadox\TableLoader\Test\Unit\Fixture\ExceptionalThings;
use Stratadox\TableLoader\Test\Unit\Fixture\Foo;
use Stratadox\TableLoader\Test\Unit\Fixture\Thing;
use Stratadox\TableLoader\Test\Unit\Fixture\Things;
use Stratadox\TableLoader\To;

/**
 * @covers \Stratadox\TableLoader\HasMany
 * @covers \Stratadox\TableLoader\UnmappableRelationship
 */
class HasMany_collects_related_objects extends TestCase
{
    /** @test */
    function making_a_collection_of_related_objects()
    {
        $relation = HasMany::in('bars');

        $data = [
            ['foo_id' => 1, 'foo_name' => 'Foo!', 'bar_id' => 1, 'bar_name' => 'Bar 1'],
            ['foo_id' => 1, 'foo_name' => 'Foo!', 'bar_id' => 2, 'bar_name' => 'Bar 2'],
            ['foo_id' => 2, 'foo_name' => 'Foo?', 'bar_id' => 2, 'bar_name' => 'Bar 2'],
            ['foo_id' => 1, 'foo_name' => 'Foo!', 'bar_id' => 3, 'bar_name' => 'Bar 3'],
        ];
        $bar1 = new Bar('Bar 1');
        $bar2 = new Bar('Bar 2');
        $bar3 = new Bar('Bar 3');
        $objects = Result::fromArray([
            'foo' => [
                '1' => new Foo('Foo!'),
                '2' => new Foo('Foo?'),
            ],
            'bar' => [
                '1' => $bar1,
                '2' => $bar2,
                '3' => $bar3,
            ],
        ]);

        $barsForFoo = $relation->load(
            From::the('foo', Identified::by('foo_id')),
            $data,
            To::the('bar', Identified::by('bar_id')),
            $objects
        )['bars'];

        $this->assertSame($bar1, $barsForFoo['1'][0]);
        $this->assertSame($bar2, $barsForFoo['1'][1]);
        $this->assertSame($bar3, $barsForFoo['1'][2]);

        $this->assertSame($bar2, $barsForFoo['2'][0]);
    }

    /** @test */
    function making_a_custom_collection_with_related_objects()
    {
        $relation = HasMany::in('things', VariadicConstructor::forThe(Things::class));

        $data = [
            ['thing_id' => 1, 'thing_name' => 'A', 'basket_name' => 'letters'],
            ['thing_id' => 2, 'thing_name' => 'B', 'basket_name' => 'letters'],
            ['thing_id' => 3, 'thing_name' => 'Foo', 'basket_name' => 'foobar'],
            ['thing_id' => 4, 'thing_name' => 'Bar', 'basket_name' => 'foobar'],
        ];

        $objects = Result::fromArray([
            'basket' => [
                'letters' => new Basket('letters', null),
                'foobar' => new Basket('foobar', null),
            ],
            'thing' => [
                'A' => new Thing(1, 'A'),
                'B' => new Thing(2, 'B'),
                'Foo' => new Thing(3, 'Foo'),
                'Bar' => new Thing(4, 'Bar'),
            ]
        ]);

        $thingsForInBasket = $relation->load(
            From::the('basket', Identified::by('basket_name')),
            $data,
            To::the('thing', Identified::by('thing_name')),
            $objects
        )['things'];

        $this->assertEquals(
            new Things(new Thing(1, 'A'), new Thing(2, 'B')),
            $thingsForInBasket['letters']
        );

        $this->assertEquals(
            new Things(new Thing(3, 'Foo'), new Thing(4, 'Bar')),
            $thingsForInBasket['foobar']
        );
    }

    /** @test */
    function ignoring_duplicate_entries()
    {
        $relation = HasMany::in('baskets');

        $data = [
            ['customer_name' => 'Alice', 'basket_name' => 'letters', 'thing_id' => 1, 'thing_name' => 'A'],
            ['customer_name' => 'Alice', 'basket_name' => 'letters', 'thing_id' => 2, 'thing_name' => 'B'],
            ['customer_name' => 'Alice', 'basket_name' => 'foobar', 'thing_id' => 3, 'thing_name' => 'Foo'],
            ['customer_name' => 'Alice', 'basket_name' => 'foobar', 'thing_id' => 4, 'thing_name' => 'Bar'],
        ];

        $objects = Result::fromArray([
            'customer' => [
                'Alice' => new Customer('Alice')
            ],
            'basket' => [
                'letters' => new Basket('letters', null),
                'foobar' => new Basket('foobar', null),
            ],
            'thing' => [
                'A' => new Thing(1, 'A'),
                'B' => new Thing(2, 'B'),
                'Foo' => new Thing(3, 'Foo'),
                'Bar' => new Thing(4, 'Bar'),
            ]
        ]);

        $basketsForCustomer = $relation->load(
            From::the('customer', Identified::by('customer_name')),
            $data,
            To::the('basket', Identified::by('basket_name')),
            $objects
        )['baskets'];

        $this->assertCount(2, $basketsForCustomer['Alice']);
    }

    /** @test */
    function throwing_an_exception_when_the_collection_could_not_be_produced()
    {
        $relation = HasMany::in('things', VariadicConstructor::forThe(ExceptionalThings::class));

        $data = [['thing_id' => 1, 'thing_name' => 'A', 'basket_name' => 'letters']];
        $objects = Result::fromArray([
            'basket' => ['letters' => new Basket('letters', null)],
            'thing' => ['A' => new Thing(1, 'A')]
        ]);

        $this->expectException(CannotLoadTable::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Could not map the `things` in the `basket` `letters`: ' .
            'Could not load the class `' . ExceptionalThings::class . '`: ' .
            'Original exception message here.'
        );

        $relation->load(
            From::the('basket', Identified::by('basket_name')),
            $data,
            To::the('thing', Identified::by('thing_name')),
            $objects
        );
    }
}
