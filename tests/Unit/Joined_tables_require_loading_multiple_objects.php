<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydrator\SimpleHydrator;
use Stratadox\TableLoader\Extract;
use Stratadox\TableLoader\From;
use Stratadox\TableLoader\HasMany;
use Stratadox\TableLoader\Identified;
use Stratadox\TableLoader\Joined;
use Stratadox\TableLoader\JoinedTable;
use Stratadox\TableLoader\Load;
use Stratadox\TableLoader\Objects;
use Stratadox\TableLoader\Prefixed;
use Stratadox\TableLoader\Test\Unit\Fixture\Bar;
use Stratadox\TableLoader\Test\Unit\Fixture\Foo;
use Stratadox\TableLoader\To;
use Stratadox\TableLoader\Wire;
use Stratadox\TableLoader\Wired;

/**
 * @covers \Stratadox\TableLoader\Joined
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
                    ->by('name')
                    ->as(Foo::class)
                    ->havingMany('bars', 'bar'),
                Load::each('bar')
                    ->by('name')
                    ->as(Bar::class)
                    ->havingMany('foos', 'foo')
            )()
        );
    }
}
