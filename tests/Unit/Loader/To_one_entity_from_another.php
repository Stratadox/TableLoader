<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Loader;

use PHPUnit\Framework\TestCase;
use Stratadox\TableLoader\Loader\Identified;
use Stratadox\TableLoader\Loader\To;

/**
 * @covers \Stratadox\TableLoader\Loader\To
 */
class To_one_entity_from_another extends TestCase
{
    /** @test */
    function knowing_where_to_map_from()
    {
        $to = To::the('foo', Identified::by('name'));
        $this->assertSame('foo', $to->label());
    }

    /** @test */
    function knowing_who_we_map_from()
    {
        $to = To::the('foo', Identified::by('name'));
        $this->assertSame('A', $to->this(['name' => 'A']));
    }

    /** @test */
    function recognising_when_to_ignore_a_row()
    {
        $to = To::the('foo', Identified::by('name'));
        $this->assertTrue($to->ignoreThe(['name' => null]));
    }

    /** @test */
    function recognising_when_not_to_ignore_a_row()
    {
        $to = To::the('foo', Identified::by('name'));
        $this->assertFalse($to->ignoreThe(['name' => 'bar']));
    }
}
