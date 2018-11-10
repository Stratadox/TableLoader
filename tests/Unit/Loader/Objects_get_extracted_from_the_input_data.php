<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Loader;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydrator\MappedHydrator;
use Stratadox\Hydrator\SimpleHydrator;
use Stratadox\IdentityMap\IdentityMap;
use Stratadox\IdentityMap\Whitelist;
use Stratadox\TableLoader\Loader\CannotLoadTable;
use Stratadox\TableLoader\Loader\Identified;
use Stratadox\TableLoader\Loader\Objects;
use Stratadox\TableLoader\Loader\Prefixed;
use Stratadox\TableLoader\Test\Unit\Fixture\Banana;
use Stratadox\TableLoader\Test\Unit\Fixture\Exceptional;
use Stratadox\TableLoader\Test\Unit\Fixture\Thing;

/**
 * @covers \Stratadox\TableLoader\Loader\Objects
 * @covers \Stratadox\TableLoader\Loader\UnmappableRow
 */
class Objects_get_extracted_from_the_input_data extends TestCase
{
    /** @test */
    function extracting_objects_from_a_table_subset()
    {
        $tableData = [
            ['thing_id' => 1, 'thing_name' => 'Foo'],
            ['thing_id' => 2, 'thing_name' => 'Bar'],
            ['thing_id' => 2, 'thing_name' => 'Ignore this data'],
            ['thing_id' => 3, 'thing_name' => 'Baz'],
        ];

        $objects = Objects::producedByThis(
            SimpleHydrator::forThe(Thing::class),
            Prefixed::with('thing'),
            Identified::by('id')
        );
        $resulting = $objects->from($tableData, IdentityMap::startEmpty());

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
    function ignoring_null_values()
    {
        $tableData = [
            ['thing_id' => 1, 'thing_name' => 'Foo'],
            ['thing_id' => null, 'thing_name' => null],
            ['thing_id' => 2, 'thing_name' => 'Bar'],
            ['thing_id' => null, 'thing_name' => 'Irrelevant'],
            ['thing_id' => null, 'thing_name' => 'Ignored'],
            ['thing_id' => 3, 'thing_name' => 'Baz'],
            ['thing_id' => null, 'thing_name' => 'Futile'],
        ];

        $objects = Objects::producedByThis(
            SimpleHydrator::forThe(Thing::class),
            Prefixed::with('thing'),
            Identified::by('id')
        );
        $resulting = $objects->from($tableData, IdentityMap::startEmpty());

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
    function using_previously_loaded_objects_from_the_identity_map()
    {
        $foo = new Thing(1, 'Foo');
        $bar = new Thing(2, 'Bar');

        $tableData = [
            ['thing_id' => 1, 'thing_name' => 'Foo'],
            ['thing_id' => 2, 'thing_name' => 'Bar'],
            ['thing_id' => 3, 'thing_name' => 'Baz'],
        ];

        $objects = Objects::producedByThis(
            SimpleHydrator::forThe(Thing::class),
            Prefixed::with('thing'),
            Identified::by('id')
        );

        $identityMap = IdentityMap::with([
            '1' => $foo
        ]);

        $resulting = $objects->from($tableData, $identityMap);

        $this->assertArrayHasKey('thing', $resulting);

        /** @var iterable|Thing[] $things */
        $things = $resulting['thing'];

        $this->assertSame($foo, $things['1']);

        $this->assertEquals($bar, $things['2']);
        $this->assertNotSame($bar, $things['2']);
    }

    /** @test */
    function loading_objects_that_are_ignored_by_the_identity_map()
    {
        $tableData = [
            ['thing_id' => 1, 'thing_name' => 'Foo'],
            ['thing_id' => 2, 'thing_name' => 'Bar'],
            ['thing_id' => 3, 'thing_name' => 'Baz'],
        ];

        $objects = Objects::producedByThis(
            SimpleHydrator::forThe(Thing::class),
            Prefixed::with('thing'),
            Identified::by('id')
        );

        $identityMap = Whitelist::the(Banana::class);

        $resulting = $objects->from($tableData, $identityMap);

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

        $this->assertFalse($resulting->identityMap()->has(Thing::class, '1'));
        $this->assertFalse($resulting->identityMap()->has(Thing::class, '2'));
        $this->assertFalse($resulting->identityMap()->has(Thing::class, '3'));
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

        $objects->from([
            ['thing_id' => 1, 'thing_name' => 'Foo']
        ], IdentityMap::startEmpty());
    }
}
