<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\TableLoader\From;
use Stratadox\TableLoader\HasMany;
use Stratadox\TableLoader\Identified;
use Stratadox\TableLoader\Test\Unit\Fixture\Bar;
use Stratadox\TableLoader\Test\Unit\Fixture\Foo;
use Stratadox\TableLoader\To;
use Stratadox\TableLoader\Wire;

/**
 * @covers \Stratadox\TableLoader\Wire
 */
class Wire_a_relationship_together extends TestCase
{
    /** @test */
    function connecting_objects()
    {
        $connection = Wire::it(
            From::the('foo', Identified::by('foo_name')),
            To::the('bar', Identified::by('bar_name')),
            HasMany::in('bars')
        );
        $data = [
            ['foo_name' => 'foo1', 'bar_name' => 'bar1'],
            ['foo_name' => 'foo1', 'bar_name' => 'bar2'],
        ];
        $foo = new Foo('foo1');
        $bar1 = new Bar('bar1');
        $bar2 = new Bar('bar2');
        $objects = [
            'foo' => ['foo1' => $foo],
            'bar' => [
                'bar1' => $bar1,
                'bar2' => $bar2,
            ],
        ];

        $connection->wire($objects, $data);

        $this->assertSame($bar1, $foo->bars()[0]);
        $this->assertSame($bar2, $foo->bars()[1]);
    }
}
