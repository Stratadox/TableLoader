<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit;

use BadMethodCallException;
use PHPUnit\Framework\TestCase;
use Stratadox\IdentityMap\IdentityMap;
use Stratadox\TableLoader\Result;
use Stratadox\TableLoader\Test\Unit\Fixture\Thing;

/**
 * @covers \Stratadox\TableLoader\Result
 */
class Result_contains_labeled_objects extends TestCase
{
    /** @test */
    function accessing_the_result_as_array()
    {
        $thing = new Thing(1, 'Foo!');
        $result = Result::fromArray([
            'thing' => [
                '#1' => $thing,
            ]
        ], IdentityMap::with([
            '#1' => $thing
        ]));

        $this->assertArrayHasKey('thing', $result);
        $this->assertSame($thing, $result['thing']['#1']);
    }

    /** @test */
    function retrieving_the_identity_map()
    {
        $identityMap = IdentityMap::startEmpty();
        $result = Result::fromArray([], $identityMap);

        $this->assertSame($identityMap, $result->identityMap());
    }

    /** @test */
    function cannot_alter_the_result_after_is_has_been_produced()
    {
        $result = Result::fromArray([], IdentityMap::startEmpty());

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Altering the results is not allowed.');

        $result['foo'] = [];
    }

    /** @test */
    function cannot_remove_from_result_after_it_has_been_produced()
    {
        $thing = new Thing(1, 'Foo!');
        $result = Result::fromArray([
            'thing' => [
                '#1' => $thing,
            ]
        ], IdentityMap::with([
            '#1' => $thing
        ]));

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Altering the results is not allowed.');

        unset($result['thing']);
    }
}

