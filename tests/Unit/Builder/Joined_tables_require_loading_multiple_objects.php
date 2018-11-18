<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Builder;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydrator\SimpleHydrator;
use Stratadox\TableLoader\Loader\Extract;
use Stratadox\TableLoader\Loader\From;
use Stratadox\TableLoader\Loader\HasMany;
use Stratadox\TableLoader\Loader\Identified;
use Stratadox\TableLoader\Builder\Joined;
use Stratadox\TableLoader\Loader\JoinedTable;
use Stratadox\TableLoader\Builder\Load;
use Stratadox\TableLoader\Loader\Objects;
use Stratadox\TableLoader\Loader\Prefixed;
use Stratadox\TableLoader\Test\Unit\Fixture\Bar;
use Stratadox\TableLoader\Test\Unit\Fixture\Foo;
use Stratadox\TableLoader\Loader\To;
use Stratadox\TableLoader\Loader\Wire;
use Stratadox\TableLoader\Loader\Wired;

/**
 * @covers \Stratadox\TableLoader\Builder\Joined
 */
class Joined_tables_require_loading_multiple_objects extends TestCase
{
    /** @test */
    function reorganising_the_objects_and_wiring()
    {
        $this->assertEquals(
            JoinedTable::converter(
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
            ),

            // Or just use this:
            Joined::table(
                Load::each('foo')
                    ->as(Foo::class)
                    ->by('name')
                    ->havingMany('bars', 'bar'),
                Load::each('bar')
                    ->as(Bar::class)
                    ->by('name')
                    ->havingMany('foos', 'foo')
            )()
        );
    }
}
