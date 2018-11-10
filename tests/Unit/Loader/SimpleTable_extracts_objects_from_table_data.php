<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Loader;

use PHPUnit\Framework\TestCase;
use Stratadox\Hydrator\MappedHydrator;
use Stratadox\Hydrator\SimpleHydrator;
use Stratadox\IdentityMap\IdentityMap;
use Stratadox\TableLoader\Loader\CannotLoadTable;
use Stratadox\TableLoader\Loader\Identified;
use Stratadox\TableLoader\Loader\SimpleTable;
use Stratadox\TableLoader\Test\Unit\Fixture\Exceptional;
use Stratadox\TableLoader\Test\Unit\Fixture\Thing;

/**
 * @covers \Stratadox\TableLoader\Loader\SimpleTable
 * @covers \Stratadox\TableLoader\Loader\UnmappableRow
 */
class SimpleTable_extracts_objects_from_table_data extends TestCase
{
    /** @test */
    function extracting_objects_from_a_table()
    {
        $makeObjects = SimpleTable::converter(
            'thing',
            SimpleHydrator::forThe(Thing::class),
            Identified::by('id')
        );

        $data = [
            ['id' => 1, 'name' => 'foo'],
            ['id' => 2, 'name' => 'bar'],
        ];

        $things = $makeObjects->from($data)['thing'];

        $this->assertEquals(new Thing(1, 'foo'), $things['1']);
        $this->assertEquals(new Thing(2, 'bar'), $things['2']);
    }

    /** @test */
    function skipping_pre_existing_objects()
    {
        $foo = new Thing(1, 'foo');
        $bar = new Thing(2, 'bar');
        $identityMap = IdentityMap::with(['1' => $foo]);
        $makeObjects = SimpleTable::converter(
            'thing',
            SimpleHydrator::forThe(Thing::class),
            Identified::by('id')
        );

        $data = [
            ['id' => 1, 'name' => 'foo'],
            ['id' => 2, 'name' => 'bar'],
        ];

        $things = $makeObjects->from($data, $identityMap)['thing'];

        $this->assertSame($foo, $things['1']);
        $this->assertNotSame($bar, $things['2']);
    }

    /** @test */
    function throwing_an_exception_when_the_identifier_is_missing()
    {
        $makeObjects = SimpleTable::converter(
            'thing',
            SimpleHydrator::forThe(Thing::class),
            Identified::by('index')
        );

        $data = [
            ['id' => 1, 'name' => 'foo'],
            ['id' => 2, 'name' => 'bar'],
        ];

        $this->expectException(CannotLoadTable::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Missing the identifying column `index` in the input data: {"id":1,"name":"foo"}'
        );

        $makeObjects->from($data);
    }

    /** @test */
    function throwing_an_exception_when_the_object_could_not_be_constructed()
    {
        $makeObjects = SimpleTable::converter(
            'thing',
            MappedHydrator::forThe(Thing::class, Exceptional::mapping(
                'Original exception message here.'
            )),
            Identified::by('id')
        );

        $this->expectException(CannotLoadTable::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Could not map the `thing` from `{"id":1,"name":"foo"}`: ' .
            'Could not load the class `' . Thing::class . '`: ' .
            'Original exception message here.'
        );

        $makeObjects->from([['id' => 1, 'name' => 'foo']], IdentityMap::startEmpty());
    }
}
