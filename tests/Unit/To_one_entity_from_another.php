<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\TableLoader\Identified;
use Stratadox\TableLoader\To;

/**
 * @covers \Stratadox\TableLoader\To
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
        $this->assertSame('#A', $to->this(['name' => 'A']));
    }
}
