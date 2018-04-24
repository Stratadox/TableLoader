<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\TableLoader\Identified;
use Stratadox\TableLoader\From;

/**
 * @covers \Stratadox\TableLoader\From
 */
class From_one_entity_to_another extends TestCase
{
    /** @test */
    function knowing_where_to_map_from()
    {
        $from = From::the('foo', Identified::by('name'));
        $this->assertSame('foo', $from->label());
    }

    /** @test */
    function knowing_who_we_map_from()
    {
        $from = From::the('foo', Identified::by('name'));
        $this->assertSame('#A', $from->this(['name' => 'A']));
    }
}
