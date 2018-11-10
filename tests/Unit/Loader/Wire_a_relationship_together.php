<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Loader;

use PHPUnit\Framework\TestCase;
use Stratadox\TableLoader\Loader\From;
use Stratadox\TableLoader\Loader\HasMany;
use Stratadox\TableLoader\Loader\HasOne;
use Stratadox\TableLoader\Loader\Identified;
use Stratadox\TableLoader\Loader\Result;
use Stratadox\TableLoader\Test\Unit\Fixture\Bar;
use Stratadox\TableLoader\Test\Unit\Fixture\Child;
use Stratadox\TableLoader\Test\Unit\Fixture\Foo;
use Stratadox\TableLoader\Test\Unit\Fixture\OtherChild;
use Stratadox\TableLoader\Test\Unit\Fixture\Thing;
use Stratadox\TableLoader\Loader\To;
use Stratadox\TableLoader\Loader\Wire;

/**
 * @covers \Stratadox\TableLoader\Loader\Wire
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
        $objects = Result::fromArray([
            'foo' => ['foo1' => $foo],
            'bar' => [
                'bar1' => $bar1,
                'bar2' => $bar2,
            ],
        ]);

        $connection->wire($objects, $data);

        $this->assertSame($bar1, $foo->bars()[0]);
        $this->assertSame($bar2, $foo->bars()[1]);
    }

    /** @test */
    function connecting_only_a_subclass()
    {
        $connection = Wire::it(
            From::onlyThe(Child::class, 'kid', Identified::by('kid_name')),
            To::the('toy', Identified::by('toy_id')),
            HasOne::in('toy')
        );
        $data = [
            ['kid_name' => 'Kid 1', 'kid_type' => 'child', 'toy_id' => 1, 'toy_name' => 'Toy 1'],
            ['kid_name' => 'Kid 2', 'kid_type' => 'other', 'toy_id' => 2, 'toy_name' => 'Toy 2'],
            ['kid_name' => 'Kid 2', 'kid_type' => 'other', 'toy_id' => 3, 'toy_name' => 'Toy 3'],
        ];
        $toy1 = new Thing(1, 'Toy 1');
        $toy2 = new Thing(2, 'Toy 2');
        $toy3 = new Thing(3, 'Toy 3');
        $kid1 = new Child('Kid 1');
        $kid2 = new OtherChild('Kid 1');

        $objects = Result::fromArray([
            'kid' => [
                'Kid 1' => $kid1,
                'Kid 2' => $kid2,
            ],
            'toy' => [
                '1' => $toy1,
                '2' => $toy2,
                '3' => $toy3,
            ],
        ]);

        $connection->wire($objects, $data);

        $this->assertSame($toy1, $kid1->toy());
        $this->assertCount(0, $kid2->toys());

        $this->assertObjectHasAttribute('toy', $kid1);
        $this->assertObjectNotHasAttribute('toy', $kid2);
    }
}
