<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydrator\SimpleHydrator;
use Stratadox\Hydrator\VariadicConstructor;
use Stratadox\IdentityMap\IdentityMap;
use Stratadox\TableLoader\Extract;
use Stratadox\TableLoader\From;
use Stratadox\TableLoader\HasMany;
use Stratadox\TableLoader\Identified;
use Stratadox\TableLoader\JoinedTable;
use Stratadox\TableLoader\Objects;
use Stratadox\TableLoader\Prefixed;
use Stratadox\TableLoader\Test\Unit\Fixture\Bar;
use Stratadox\TableLoader\Test\Unit\Fixture\Basket;
use Stratadox\TableLoader\Test\Unit\Fixture\Foo;
use Stratadox\TableLoader\Test\Unit\Fixture\Thing;
use Stratadox\TableLoader\Test\Unit\Fixture\Things;
use Stratadox\TableLoader\To;
use Stratadox\TableLoader\Wire;
use Stratadox\TableLoader\Wired;

/**
 * @covers \Stratadox\TableLoader\JoinedTable
 */
class JoinedTable_extracts_connected_objects_from_a_joined_result extends TestCase
{
    /** @test */
    function extracting_bidirectional_ManyToMany_objects_from_a_joined_result()
    {
        $makeObjects = JoinedTable::converter(
            Extract::these(
                Objects::producedByThis(
                    SimpleHydrator::forThe(Foo::class),
                    Prefixed::with('foo'),
                    Identified::by('name')
                ),
                Objects::producedByThis(
                    SimpleHydrator::forThe(Bar::class),
                    Prefixed::with('bar'),
                    Identified::by('name')
                )
            ),
            Wired::together(
                Wire::it(
                    From::the('foo', Identified::by('foo_name')),
                    To::the('bar', Identified::by('bar_name')),
                    HasMany::in('bars')
                ),
                Wire::it(
                    From::the('bar', Identified::by('bar_name')),
                    To::the('foo', Identified::by('foo_name')),
                    HasMany::in('foos')
                )
            )
        );

        $tableData = [
            ['foo_name' => 'Foo 1', 'bar_name' => 'Bar 1'],
            ['foo_name' => 'Foo 1', 'bar_name' => 'Bar 2'],
            ['foo_name' => 'Foo 2', 'bar_name' => 'Bar 2'],
            ['foo_name' => 'Foo 3', 'bar_name' => 'Bar 2'],
            ['foo_name' => 'Foo 3', 'bar_name' => 'Bar 3'],
            ['foo_name' => 'Foo 3', 'bar_name' => 'Bar 4'],
        ];

        $objects = $makeObjects->from($tableData);

        /** @var iterable|Foo[] $foos */
        $foos = $objects['foo'];

        $this->assertCount(3, $foos, 'Expecting 3 Foo objects.');
        foreach ($foos as $foo) {
            $this->assertInstanceOf(Foo::class, $foo, 'Expecting Foo objects.');
        }

        $this->assertCount(2, $foos['Foo 1']->bars(), 'Expecting Foo 1 to have two Bars.');
        $this->assertCount(1, $foos['Foo 2']->bars(), 'Expecting Foo 2 to have one Bar.');
        $this->assertCount(3, $foos['Foo 3']->bars(), 'Expecting Foo 3 to have three Bars.');

        foreach ($foos as $foo) {
            foreach ($foo->bars() as $bar) {
                $this->assertInstanceOf(Bar::class, $bar, 'Expecting Bar objects.');
            }
        }

        $this->assertSame(
            $foos['Foo 1']->bars()[1],
            $foos['Foo 2']->bars()[0],
            'Expecting Foo 1 and Foo 2 to share a Bar instance.'
        );

        /** @var iterable|Bar[] $bars */
        $bars = $objects['bar'];

        $this->assertCount(4, $bars, 'Expecting 4 Bar objects');
        foreach ($bars as $bar) {
            $this->assertInstanceOf(Bar::class, $bar, 'Expecting Bar objects.');
        }

        $this->assertCount(1, $bars['Bar 1']->foos(), 'Expecting Bar 1 to have one Foo.');
        $this->assertCount(3, $bars['Bar 2']->foos(), 'Expecting Bar 2 to have three Foos.');
        $this->assertCount(1, $bars['Bar 3']->foos(), 'Expecting Bar 3 to have one Foo.');
        $this->assertCount(1, $bars['Bar 4']->foos(), 'Expecting Bar 4 to have one Foo.');
    }

    /** @test */
    function extracting_unidirectional_OneToMany_objects_from_a_joined_result()
    {
        $makeObjects = JoinedTable::converter(
            Extract::these(
                Objects::producedByThis(
                    SimpleHydrator::forThe(Thing::class),
                    Prefixed::with('thing'),
                    Identified::by('id')
                ),
                Objects::producedByThis(
                    SimpleHydrator::forThe(Basket::class),
                    Prefixed::with('basket'),
                    Identified::by('name')
                )
            ),
            Wired::together(
                Wire::it(
                    From::the('basket', Identified::by('basket_name')),
                    To::the('thing', Identified::by('thing_id')),
                    HasMany::in('things', VariadicConstructor::forThe(Things::class))
                )
            )
        );

        $tableData = [
            ['thing_id' => 1, 'thing_name' => 'Foo', 'basket_name' => 'foobar'],
            ['thing_id' => 2, 'thing_name' => 'Bar', 'basket_name' => 'foobar'],
            ['thing_id' => 3, 'thing_name' => 'Baz', 'basket_name' => 'foobar'],
            ['thing_id' => 4, 'thing_name' => 'A', 'basket_name' => 'letters'],
            ['thing_id' => 5, 'thing_name' => 'B', 'basket_name' => 'letters'],
            ['thing_id' => 6, 'thing_name' => 'C', 'basket_name' => 'letters'],
            ['thing_id' => 7, 'thing_name' => 'D', 'basket_name' => 'letters'],
        ];

        $baskets = $makeObjects->from($tableData)['basket'];

        $this->assertEquals(
            new Basket('foobar', new Things(
                new Thing(1, 'Foo'),
                new Thing(2, 'Bar'),
                new Thing(3, 'Baz')
            )),
            $baskets['foobar']
        );

        $this->assertEquals(
            new Basket('letters', new Things(
                new Thing(4, 'A'),
                new Thing(5, 'B'),
                new Thing(6, 'C'),
                new Thing(7, 'D')
            )),
            $baskets['letters']
        );
    }

    /** @test */
    function using_previously_loaded_objects_from_the_identity_map()
    {
        $existingBasket = new Basket('foobar', new Things);

        $identityMap = IdentityMap::with([
            'foobar' => $existingBasket,
        ]);

        $makeObjects = JoinedTable::converter(
            Extract::these(
                Objects::producedByThis(
                    SimpleHydrator::forThe(Thing::class),
                    Prefixed::with('thing'),
                    Identified::by('id')
                ),
                Objects::producedByThis(
                    SimpleHydrator::forThe(Basket::class),
                    Prefixed::with('basket'),
                    Identified::by('name')
                )
            ),
            Wired::together(
                Wire::it(
                    From::the('basket', Identified::by('basket_name')),
                    To::the('thing', Identified::by('thing_id')),
                    HasMany::in('things', VariadicConstructor::forThe(Things::class))
                )
            )
        );

        $tableData = [
            ['thing_id' => 1, 'thing_name' => 'Foo', 'basket_name' => 'foobar'],
            ['thing_id' => 2, 'thing_name' => 'Bar', 'basket_name' => 'foobar'],
            ['thing_id' => 3, 'thing_name' => 'Baz', 'basket_name' => 'foobar'],
        ];

        $baskets = $makeObjects->from($tableData, $identityMap)['basket'];

        $this->assertSame($existingBasket, $baskets['foobar']);
        $this->assertCount(3, $existingBasket->things());
    }
}
