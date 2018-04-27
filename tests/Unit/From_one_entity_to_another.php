<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\TableLoader\Identified;
use Stratadox\TableLoader\From;
use Stratadox\TableLoader\Test\Unit\Fixture\Bar;
use Stratadox\TableLoader\Test\Unit\Fixture\Foo;

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
        $this->assertSame('A', $from->this(['name' => 'A']));
    }

    /** @test */
    function knowing_which_concrete_class_gets_this_relation()
    {
        $from = From::onlyThe(Foo::class, 'foo', Identified::by('name'));
        $this->assertTrue($from->hereToo(new Foo('foo')));
        $this->assertFalse($from->hereToo(new Bar('not actually foo')));
    }
}
