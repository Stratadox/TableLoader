<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Builder;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydration\Mapper\Instruction\Is;
use Stratadox\Hydration\Mapping\Properties;
use Stratadox\Hydration\Mapping\Property\Scalar\IntegerValue;
use Stratadox\Hydration\Mapping\Property\Scalar\StringValue;
use Stratadox\Hydrator\MappedHydrator;
use Stratadox\Hydrator\SimpleHydrator;
use Stratadox\Hydrator\VariadicConstructor;
use Stratadox\TableLoader\Builder\CannotMakeTableMapping;
use Stratadox\TableLoader\Loader\From;
use Stratadox\TableLoader\Loader\HasMany;
use Stratadox\TableLoader\Loader\HasOne;
use Stratadox\TableLoader\Loader\Identified;
use Stratadox\TableLoader\Builder\Load;
use Stratadox\TableLoader\Loader\Objects;
use Stratadox\TableLoader\Loader\Prefixed;
use Stratadox\TableLoader\Test\Unit\Fixture\Basket;
use Stratadox\TableLoader\Test\Unit\Fixture\Baz;
use Stratadox\TableLoader\Test\Unit\Fixture\Foo;
use Stratadox\TableLoader\Test\Unit\Fixture\Member;
use Stratadox\TableLoader\Test\Unit\Fixture\Thing;
use Stratadox\TableLoader\Test\Unit\Fixture\Things;
use Stratadox\TableLoader\Loader\To;
use Stratadox\TableLoader\Loader\Wire;
use Stratadox\TableLoader\Loader\Wired;

/**
 * @covers \Stratadox\TableLoader\Builder\Load
 * @covers \Stratadox\TableLoader\Builder\CannotMakeMapping
 * @covers \Stratadox\TableLoader\Builder\CannotProduceObjects
 */
class Load_objects_according_to_the_mapping extends TestCase
{
    /** @test */
    function defining_simple_object_creation()
    {
        $howToLoad = Load::each('thing')
            ->by('id')
            ->as(Thing::class);

        $this->assertEquals(
            Objects::producedByThis(
                SimpleHydrator::forThe(Thing::class),
                Prefixed::with('thing'),
                Identified::by('id')
            ),
            $howToLoad->objects()
        );
    }

    /** @test */
    function finding_the_label()
    {
        $howToLoad = Load::each('thing')
            ->by('id')
            ->as(Thing::class);

        $this->assertEquals('thing', $howToLoad->label());
    }

    /** @test */
    function finding_the_identifying_columns()
    {
        $howToLoad = Load::each('thing')
            ->as(Thing::class);

        $this->assertEquals(['thing_id'], $howToLoad->identityColumns());
    }

    /** @test */
    function finding_the_custom_identifying_columns()
    {
        $howToLoad = Load::each('thing')
            ->by('name', 'id')
            ->as(Thing::class);

        $this->assertEquals(['thing_name', 'thing_id'], $howToLoad->identityColumns());
    }

    /** @test */
    function defining_mapped_object_creation()
    {
        $howToLoad = Load::each('thing')
            ->as(Thing::class, [
                'id' => Is::int(),
                'name' => Is::string()
            ]);

        $this->assertEquals(
            Objects::producedByThis(
                MappedHydrator::forThe(Thing::class, Properties::map(
                    IntegerValue::inProperty('id'),
                    StringValue::inProperty('name')
                )),
                Prefixed::with('thing'),
                Identified::by('id')
            ),
            $howToLoad->objects()
        );
    }

    /** @test */
    function defining_an_object_without_wiring()
    {
        $howToLoad = Load::each('thing')
            ->by('id')
            ->as(Thing::class);

        $this->assertEmpty($howToLoad->wiring());
    }

    /** @test */
    function defining_has_many_object_wiring()
    {
        $howToLoad = Load::each('foo')
            ->by('name')
            ->as(Foo::class)
            ->havingMany('bars', 'bar')
            ->identifying('bar', 'bar_name');

        $this->assertEquals(
            Wire::it(
                From::the('foo', Identified::by('foo_name')),
                To::the('bar', Identified::by('bar_name')),
                HasMany::in('bars')
            ),
            $howToLoad->wiring()
        );
    }

    /** @test */
    function defining_has_many_object_wiring_with_custom_collection_object()
    {
        $howToLoad = Load::each('basket')
            ->by('name')
            ->as(Basket::class)
            ->havingMany('things', 'thing', Things::class)
            ->identifying('thing', 'thing_id');

        $this->assertEquals(
            Wire::it(
                From::the('basket', Identified::by('basket_name')),
                To::the('thing', Identified::by('thing_id')),
                HasMany::in('things', VariadicConstructor::forThe(Things::class))
            ),
            $howToLoad->wiring()
        );
    }

    /** @test */
    function defining_has_one_object_wiring()
    {
        $howToLoad = Load::each('member')
            ->by('name')
            ->as(Member::class)
            ->havingOne('group', 'group')
            ->identifying('group', 'group_name');

        $this->assertEquals(
            Wire::it(
                From::the('member', Identified::by('member_name')),
                To::the('group', Identified::by('group_name')),
                HasOne::in('group')
            ),
            $howToLoad->wiring()
        );
    }

    /** @test */
    function defining_multiple_object_relations()
    {
        $howToLoad = Load::each('baz')
            ->by('name')
            ->as(Baz::class)
            ->havingMany('foos', 'foo')
            ->havingMany('bars', 'bar')
            ->identifying('foo', 'foo_name')
            ->identifying('bar', 'bar_name');

        $this->assertEquals(
            Wired::together(
                Wire::it(
                    From::the('baz', Identified::by('baz_name')),
                    To::the('foo', Identified::by('foo_name')),
                    HasMany::in('foos')
                ),
                Wire::it(
                    From::the('baz', Identified::by('baz_name')),
                    To::the('bar', Identified::by('bar_name')),
                    HasMany::in('bars')
                )
            ),
            $howToLoad->wiring()
        );
    }

    /** @test */
    function throwing_an_exception_when_the_relation_could_not_be_identified()
    {
        $howToLoad = Load::each('baz')
            ->by('name')
            ->as(Baz::class)
            ->havingMany('foos', 'foo')
            ->havingMany('bars', 'bar');

        $this->expectException(CannotMakeTableMapping::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Cannot make a mapping for the `baz` objects: ' .
            'Cannot resolve the identifying columns for the `foo` relation.'
        );

        $howToLoad->wiring();
    }

    /** @test */
    function throwing_an_exception_when_the_class_cannot_be_produced()
    {
        $howToLoad = Load::each('thing')
            ->by('id')
            ->as('non-existing class');

        $this->expectException(CannotMakeTableMapping::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Cannot produce the `thing` objects: ' .
            'Could not create instantiator: ' .
            'Class non-existing class does not exist.'
        );

        $howToLoad->objects();
    }

    /** @test */
    function throwing_an_exception_when_the_mapped_class_cannot_be_produced()
    {
        $howToLoad = Load::each('thing')
            ->by('id')
            ->as('non-existing class', ['name' => Is::string()]);

        $this->expectException(CannotMakeTableMapping::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Cannot produce the `thing` objects: ' .
            'Could not produce mapping for non-existing ' .
            'class `non-existing class`'
        );

        $howToLoad->objects();
    }
}
