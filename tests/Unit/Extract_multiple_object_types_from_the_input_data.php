<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydrator\SimpleHydrator;
use Stratadox\TableLoader\Identified;
use Stratadox\TableLoader\Objects;
use Stratadox\TableLoader\Extract;
use Stratadox\TableLoader\Prefixed;
use Stratadox\TableLoader\Test\Unit\Fixture\Bar;
use Stratadox\TableLoader\Test\Unit\Fixture\Foo;

/**
 * @covers \Stratadox\TableLoader\Extract
 */
class Extract_multiple_object_types_from_the_input_data extends TestCase
{
    /** @test */
    function converting_a_table_into_a_set_of_objects()
    {
        $tableData = [
            ['foo_name' => 'Foo 1', 'bar_name' => 'Bar 1'],
            ['foo_name' => 'Foo 1', 'bar_name' => 'Bar 2'],
            ['foo_name' => 'Foo 2', 'bar_name' => 'Bar 2'],
            ['foo_name' => 'Foo 3', 'bar_name' => 'Bar 3'],
            ['foo_name' => 'Foo 3', 'bar_name' => 'Bar 4'],
        ];

        $extract = Extract::these(
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
        );

        $objects = $extract->from($tableData);

        $this->assertArrayHasKey('foo', $objects);
        $this->assertArrayHasKey('bar', $objects);

        $foos = $objects['foo'];
        $bars = $objects['bar'];

        $this->assertCount(3, $foos);
        $this->assertCount(4, $bars);
    }
}
