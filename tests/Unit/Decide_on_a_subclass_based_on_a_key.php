<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydration\Mapper\Instruction\Has;
use Stratadox\Hydration\Mapper\Instruction\Is;
use Stratadox\Hydration\Mapping\Properties;
use Stratadox\Hydration\Mapping\Property\Relationship\HasOneEmbedded;
use Stratadox\Hydration\Mapping\Property\Scalar\IntegerValue;
use Stratadox\Hydration\Mapping\Property\Scalar\StringValue;
use Stratadox\Hydrator\MappedHydrator;
use Stratadox\Hydrator\OneOfTheseHydrators;
use Stratadox\Hydrator\SimpleHydrator;
use Stratadox\Hydrator\VariadicConstructor;
use Stratadox\TableLoader\Decide;
use Stratadox\TableLoader\From;
use Stratadox\TableLoader\HasMany;
use Stratadox\TableLoader\HasOne;
use Stratadox\TableLoader\Identified;
use Stratadox\TableLoader\InCase;
use Stratadox\TableLoader\Objects;
use Stratadox\TableLoader\Prefixed;
use Stratadox\TableLoader\Test\Unit\Fixture\Apple;
use Stratadox\TableLoader\Test\Unit\Fixture\Backpack;
use Stratadox\TableLoader\Test\Unit\Fixture\Banana;
use Stratadox\TableLoader\Test\Unit\Fixture\Basket;
use Stratadox\TableLoader\Test\Unit\Fixture\Child;
use Stratadox\TableLoader\Test\Unit\Fixture\Dimensions;
use Stratadox\TableLoader\Test\Unit\Fixture\Genre;
use Stratadox\TableLoader\Test\Unit\Fixture\Film;
use Stratadox\TableLoader\Test\Unit\Fixture\OtherChild;
use Stratadox\TableLoader\Test\Unit\Fixture\Television;
use Stratadox\TableLoader\Test\Unit\Fixture\Things;
use Stratadox\TableLoader\To;
use Stratadox\TableLoader\Wire;
use Stratadox\TableLoader\Wired;

/**
 * @covers \Stratadox\TableLoader\Decide
 */
class Decide_on_a_subclass_based_on_a_key extends TestCase
{
    /** @test */
    function defining_an_inheritance_structure()
    {
        $howToLoad = Decide::which('kid')->basedOn(
            'type',
            InCase::of('child')->as(Child::class),
            InCase::of('other')->as(OtherChild::class)
        );

        $this->assertEquals(
            Objects::producedByThis(
                OneOfTheseHydrators::decideBasedOnThe('type', [
                    'child' => SimpleHydrator::forThe(Child::class),
                    'other' => SimpleHydrator::forThe(OtherChild::class),
                ]),
                Prefixed::with('kid'),
                Identified::by('id')->andForLoading('type')
            ),
            $howToLoad->objects()
        );
    }

    /** @test */
    function finding_the_label()
    {
        $howToLoad = Decide::which('kid')->basedOn(
            'type',
            InCase::of('child')->as(Child::class),
            InCase::of('other')->as(OtherChild::class)
        );

        $this->assertSame('kid', $howToLoad->label());
    }

    /** @test */
    function finding_the_identifying_columns()
    {
        $howToLoad = Decide::which('kid')->basedOn(
            'type',
            InCase::of('child')->as(Child::class),
            InCase::of('other')->as(OtherChild::class)
        );

        $this->assertSame(['kid_type', 'kid_id'], $howToLoad->identityColumns());
    }

    /** @test */
    function finding_the_custom_identifying_columns()
    {
        $howToLoad = Decide::which('kid')->basedOn(
            'type',
            InCase::of('child')->as(Child::class),
            InCase::of('other')->as(OtherChild::class)
        )->by('ssn');

        $this->assertSame(['kid_type', 'kid_ssn'], $howToLoad->identityColumns());
    }

    /** @test */
    function defining_mapped_object_creation()
    {
        $howToLoad = Decide::which('product')
            ->basedOn('type', ...[
                InCase::of('television')->as(Television::class, [
                    'brand' => Is::string(),
                    'size' => Has::one(Dimensions::class)
                        ->with('height', Is::int())
                        ->with('width', Is::int())
                        ->with('depth', Is::int())
                ]),
                InCase::of('film')->as(Film::class, [
                    'producer' => Is::string(),
                    'genre' => Has::one(Genre::class)->with('name')
                ])
            ])
            ->with([
                'name' => Is::string(),
                'price' => Is::int()
            ]);

        $this->assertEquals(
            Objects::producedByThis(
                OneOfTheseHydrators::decideBasedOnThe('type', [
                    'television' => MappedHydrator::forThe(
                        Television::class,
                        Properties::map(
                            StringValue::inProperty('brand'),
                            HasOneEmbedded::inProperty(
                                'size',
                                MappedHydrator::forThe(
                                    Dimensions::class,
                                    Properties::map(
                                        IntegerValue::inProperty('height'),
                                        IntegerValue::inProperty('width'),
                                        IntegerValue::inProperty('depth')
                                    )
                                )
                            ),
                            StringValue::inProperty('name'),
                            IntegerValue::inProperty('price')
                        )
                    ),
                    'film' => MappedHydrator::forThe(
                        Film::class,
                        Properties::map(
                            StringValue::inProperty('producer'),
                            HasOneEmbedded::inProperty(
                                'genre',
                                MappedHydrator::forThe(
                                    Genre::class,
                                    Properties::map(
                                        StringValue::inProperty('name')
                                    )
                                )
                            ),
                            StringValue::inProperty('name'),
                            IntegerValue::inProperty('price')
                        )
                    ),
                ]),
                Prefixed::with('product'),
                Identified::by('id')->andForLoading('type')
            ),
            $howToLoad->objects()
        );
    }

    /** @test */
    function defining_an_object_without_wiring()
    {
        $howToLoad = Decide::which('kid')->basedOn(
            'type',
            InCase::of('child')->as(Child::class),
            InCase::of('other')->as(OtherChild::class)
        );

        $this->assertEmpty($howToLoad->wiring());
    }

    /** @test */
    function defining_a_has_many_object_wiring()
    {
        $howToLoad = Decide::which('container')
            ->basedOn('type', ...[
                InCase::of('basket')->as(Basket::class, ['name' => Is::string()]),
                InCase::of('backpack')->as(Backpack::class)
            ])
            ->havingMany('things', 'thing', Things::class)
            ->identifying('thing', 'thing_id');

        $this->assertEquals(
            Wire::it(
                From::the('container', Identified::by('container_type', 'container_id')),
                To::the('thing', Identified::by('thing_id')),
                HasMany::in('things', VariadicConstructor::forThe(Things::class))
            ),
            $howToLoad->wiring()
        );
    }

    /** @test */
    function defining_a_has_one_object_wiring()
    {
        $howToLoad = Decide::which('fruit')->basedOn(
            'kind',
            InCase::of('apple')->as(Apple::class, ['colour' => Is::string()]),
            InCase::of('banana')->as(Banana::class, ['curve' => Is::int()])
        )->havingOne('countryOfOrigin', 'country')->identifying('country', 'country_code');

        $this->assertEquals(
            Wire::it(
                From::the('fruit', Identified::by('fruit_type', 'fruit_id')),
                To::the('country', Identified::by('country_code')),
                HasOne::in('countryOfOrigin')
            ),
            $howToLoad->wiring()
        );
    }

    /** @test */
    function defining_either_has_one_or_has_many_object_wiring()
    {
        $howToLoad = Decide::which('kid')->basedOn(
            'type',
            InCase::of('child')->as(Child::class)->havingOne('toy'),
            InCase::of('other')->as(OtherChild::class)->havingMany('toys', 'toy')
        )->identifying('toy', 'toy_name');

        $this->assertEquals(
            Wired::together(
                Wire::it(
                    From::onlyThe(Child::class, 'kid', Identified::by(
                        'kid_type',
                        'kid_id'
                    )),
                    To::the('toy', Identified::by('toy_name')),
                    HasOne::in('toy')
                ),
                Wire::it(
                    From::onlyThe(OtherChild::class, 'kid', Identified::by(
                        'kid_type',
                        'kid_id'
                    )),
                    To::the('toy', Identified::by('toy_name')),
                    HasMany::in('toys')
                )
            ),
            $howToLoad->wiring()
        );
    }

    /** @test */
    function defining_a_conditional_has_many_object_wiring()
    {
        $howToLoad = Decide::which('kid')->basedOn(
            'type',
            InCase::of('child')->as(Child::class),
            InCase::of('other')->as(OtherChild::class)->havingMany('toys', 'toy')
        )->identifying('toy', 'toy_name');

        $this->assertEquals(
            Wire::it(
                From::onlyThe(OtherChild::class, 'kid', Identified::by(
                    'kid_type',
                    'kid_id'
                )),
                To::the('toy', Identified::by('toy_name')),
                HasMany::in('toys')
            ),
            $howToLoad->wiring()
        );
    }
}
