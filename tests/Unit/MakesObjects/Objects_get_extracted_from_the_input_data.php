<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\MakesObjects;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydrator\MappedHydrator;
use Stratadox\Hydrator\SimpleHydrator;
use Stratadox\TableLoader\CannotLoadTable;
use Stratadox\TableLoader\Identified;
use Stratadox\TableLoader\Objects;
use Stratadox\TableLoader\Prefixed;
use Stratadox\TableLoader\Test\Unit\Fixture\Exceptional;
use Stratadox\TableLoader\Test\Unit\Fixture\Thing;

/**
 * @covers \Stratadox\TableLoader\Objects
 * @covers \Stratadox\TableLoader\UnmappableRow
 */
class Objects_get_extracted_from_the_input_data extends TestCase
{
    /** @test */
    function extracting_objects_from_a_table_subset()
    {
        $tableData = [
            ['thing_id' => 1, 'thing_name' => 'Foo'],
            ['thing_id' => 2, 'thing_name' => 'Bar'],
            ['thing_id' => 2, 'thing_name' => 'Ignore this one'],
            ['thing_id' => 3, 'thing_name' => 'Baz'],
        ];

        $objects = Objects::producedByThis(
            SimpleHydrator::forThe(Thing::class),
            Prefixed::with('thing'),
            Identified::by('id')
        );
        $resulting = $objects->from($tableData);

        $this->assertArrayHasKey('thing', $resulting);

        /** @var iterable|Thing[] $things */
        $things = $resulting['thing'];

        $this->assertCount(3, $things);

        $this->assertSame('Foo', $things['1']->name());
        $this->assertSame('Bar', $things['2']->name());
        $this->assertSame('Baz', $things['3']->name());

        $this->assertSame(1, $things['1']->id());
        $this->assertSame(2, $things['2']->id());
        $this->assertSame(3, $things['3']->id());
    }

    /** @test */
    function throwing_an_exception_when_the_input_could_not_be_mapped()
    {
        $objects = Objects::producedByThis(
            MappedHydrator::forThe(Thing::class, Exceptional::mapping(
                'Original exception message here.'
            )),
            Prefixed::with('thing'),
            Identified::by('id')
        );

        $this->expectException(CannotLoadTable::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Could not map the `thing` from `{"id":1,"name":"Foo"}`: ' .
            'Could not load the class `' . Thing::class . '`: ' .
            'Original exception message here.'
        );

        $objects->from([['thing_id' => 1, 'thing_name' => 'Foo']]);
    }
}
