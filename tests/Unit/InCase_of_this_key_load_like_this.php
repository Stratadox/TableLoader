<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydration\Mapper\Instruction\Has;
use Stratadox\Hydration\Mapper\Instruction\Is;
use Stratadox\Hydration\Mapping\Properties;
use Stratadox\Hydration\Mapping\Property\Relationship\HasOneEmbedded;
use Stratadox\Hydration\Mapping\Property\Scalar\StringValue;
use Stratadox\Hydrator\MappedHydrator;
use Stratadox\Hydrator\SimpleHydrator;
use Stratadox\Hydrator\VariadicConstructor;
use Stratadox\TableLoader\CannotMakeTableMapping;
use Stratadox\TableLoader\From;
use Stratadox\TableLoader\HasMany;
use Stratadox\TableLoader\HasOne;
use Stratadox\TableLoader\Identified;
use Stratadox\TableLoader\InCase;
use Stratadox\TableLoader\Test\Unit\Fixture\Child;
use Stratadox\TableLoader\Test\Unit\Fixture\Thing;
use Stratadox\TableLoader\Test\Unit\Fixture\Things;
use Stratadox\TableLoader\To;
use Stratadox\TableLoader\Wire;
use Stratadox\TableLoader\Wired;

/**
 * @covers \Stratadox\TableLoader\InCase
 * @covers \Stratadox\TableLoader\CannotMakeMapping
 */
class InCase_of_this_key_load_like_this extends TestCase
{
    /** @test */
    function retrieving_the_decision_trigger()
    {
        $howToLoad = InCase::of('child')->as(Child::class);

        $this->assertSame('child', $howToLoad->decisionTrigger());
    }

    /** @test */
    function defining_a_subclass()
    {
        $howToLoad = InCase::of('child')->as(Child::class);

        $this->assertEquals(
            SimpleHydrator::forThe(Child::class),
            $howToLoad->hydrator()
        );
    }

    /** @test */
    function defining_a_subclass_with_properties()
    {
        $howToLoad = InCase::of('child')->as(Child::class, [
            'name' => Is::string()
        ]);

        $this->assertEquals(
            MappedHydrator::forThe(Child::class, Properties::map(
                StringValue::inProperty('name')
            )),
            $howToLoad->hydrator()
        );
    }

    /** @test */
    function defining_a_subclass_with_added_properties()
    {
        $howToLoad = InCase::of('child')
            ->as(Child::class, ['toy' => Has::one(Thing::class)])
            ->with(['name' => Is::string()]);

        $this->assertEquals(
            MappedHydrator::forThe(Child::class, Properties::map(
                HasOneEmbedded::inProperty('toy', MappedHydrator::forThe(
                    Thing::class,
                    Properties::map())
                ),
                StringValue::inProperty('name')
            )),
            $howToLoad->hydrator()
        );
    }

    /** @test */
    function defining_a_subclass_with_has_one_relation()
    {
        $howToLoad = InCase::of('child')->as(Child::class)
            ->havingOne('toy')
            ->identifying('toy', 'toy_id')
            ->identifying('kid', 'kid_id')
            ->labeled('kid');

        $this->assertEquals(
            Wire::it(
                From::onlyThe(Child::class, 'kid', Identified::by('kid_id')),
                To::the('toy', Identified::by('toy_id')),
                HasOne::in('toy')
            ),
            $howToLoad->wiring()
        );
    }

    /** @test */
    function defining_a_subclass_with_has_many_relation()
    {
        $howToLoad = InCase::of('child')->as(Child::class)
            ->havingMany('toys', 'toy')
            ->identifying('toy', 'toy_id')
            ->identifying('kid', 'kid_id')
            ->labeled('kid');

        $this->assertEquals(
            Wire::it(
                From::onlyThe(Child::class, 'kid', Identified::by('kid_id')),
                To::the('toy', Identified::by('toy_id')),
                HasMany::in('toys')
            ),
            $howToLoad->wiring()
        );
    }

    /** @test */
    function defining_a_subclass_with_has_many_relation_in_custom_collection_class()
    {
        $howToLoad = InCase::of('child')->as(Child::class)
            ->havingMany('toys', 'toy', Things::class)
            ->identifying('toy', 'toy_id')
            ->identifying('kid', 'kid_id')
            ->labeled('kid');

        $this->assertEquals(
            Wire::it(
                From::onlyThe(Child::class, 'kid', Identified::by('kid_id')),
                To::the('toy', Identified::by('toy_id')),
                HasMany::in('toys', VariadicConstructor::forThe(Things::class))
            ),
            $howToLoad->wiring()
        );
    }

    /** @test */
    function defining_a_subclass_with_no_relations()
    {
        $howToLoad = InCase::of('child')->as(Child::class)
            ->identifying('kid', 'kid_id')
            ->labeled('kid');

        $this->assertEquals(
            Wired::together(),
            $howToLoad->wiring()
        );
    }

    /** @test */
    function throwing_an_exception_when_the_label_is_not_provided()
    {
        $howToLoad = InCase::of('child')->as(Child::class)
            ->identifying('kid', 'kid_id');

        $this->expectException(CannotMakeTableMapping::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Cannot make a mapping for the `child` objects: ' .
            'The label for the source relation was not provided.'
        );

        $howToLoad->wiring();
    }

    /** @test */
    function throwing_an_exception_when_the_relation_could_not_be_identified()
    {
        $howToLoad = InCase::of('child')->as(Child::class)
            ->havingMany('toys', 'toy', Things::class)
            ->identifying('kid', 'kid_id')
            ->labeled('kid');

        $this->expectException(CannotMakeTableMapping::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Cannot make a mapping for the `kid` objects: ' .
            'Cannot resolve the identifying columns for the `toy` relation.'
        );

        $howToLoad->wiring();
    }
}
