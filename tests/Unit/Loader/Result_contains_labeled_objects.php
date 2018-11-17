<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Loader;

use BadMethodCallException;
use PHPUnit\Framework\TestCase;
use Stratadox\IdentityMap\IdentityMap;
use Stratadox\TableLoader\Loader\Result;
use Stratadox\TableLoader\Test\Unit\Fixture\Thing;

/**
 * @covers \Stratadox\TableLoader\Loader\Result
 */
class Result_contains_labeled_objects extends TestCase
{
    /** @test */
    function accessing_the_result_as_array()
    {
        $thing = new Thing(1, 'Foo!');
        $result = Result::fromArray([
            'thing' => [
                '1' => $thing,
            ]
        ], IdentityMap::with([
            '1' => $thing
        ]));

        $this->assertArrayHasKey('thing', $result);
        $this->assertSame($thing, $result['thing']['1']);
    }

    /** @test */
    function retrieving_the_identity_map()
    {
        $identityMap = IdentityMap::startEmpty();
        $result = Result::fromArray([], $identityMap);

        $this->assertSame($identityMap, $result->identityMap());
    }

    /** @test */
    function checking_if_an_object_is_already_loaded()
    {
        $thing = new Thing(1, 'Foo!');
        $result = Result::fromArray([
            'thing' => [
                '1' => $thing,
            ]
        ], IdentityMap::with([
            '1' => $thing,
        ]));

        $this->assertTrue($result->has(Thing::class, '1'));
        $this->assertFalse($result->has(Thing::class, '2'));
    }

    /** @test */
    function checking_if_an_object_was_loaded_with_this_result()
    {
        $thing = new Thing(1, 'Foo!');
        $result = Result::fromArray([
            'thing' => [
                '1' => $thing,
            ]
        ], IdentityMap::with([
            '1' => $thing,
            '2' => $thing,
        ]));

        $this->assertTrue($result->has(Thing::class, '1'));
        $this->assertTrue($result->has(Thing::class, '2'));
        $this->assertTrue(isset($result['thing']['1']));
        $this->assertFalse(isset($result['thing']['2']));
    }

    /** @test */
    function adding_an_object_to_the_result()
    {
        $foo = new Thing(1, 'Foo!');
        $result = Result::fromArray([])
            ->add('thing', 'thing:1', '1', $foo);

        $this->assertTrue($result->has(Thing::class, '1'));
        $this->assertSame($foo, $result['thing']['thing:1']);
    }

    /** @test */
    function remembering_previous_results()
    {
        $foo = new Thing(1, 'Foo!');
        $result = Result::fromArray([
            'thing' => [
                'thing:1' => $foo,
            ]
        ], IdentityMap::with([
            '1' => $foo,
        ]));
        $result = $result->add('thing', 'thing:2', '2',  new Thing(2, 'Bar!'));

        $this->assertTrue($result->has(Thing::class, '1'));
        $this->assertSame($foo, $result['thing']['thing:1']);
    }

    /** @test */
    function merging_results()
    {
        $foo = new Thing(1, 'Foo!');
        $firstResult = Result::fromArray([
            'thing' => [
                'thing:1' => $foo,
            ]
        ], IdentityMap::with([
            '1' => $foo,
        ]));
        $bar = new Thing(2, 'Bar!');
        $secondResult = Result::fromArray([
            'thing' => [
                'thing:2' => $bar,
            ]
        ], IdentityMap::with([
            '1' => $foo,
            '2' => $bar,
        ]));

        $finalResult = $firstResult->mergeWith($secondResult);

        $this->assertTrue($finalResult->has(Thing::class, '1'));
        $this->assertTrue($finalResult->has(Thing::class, '2'));
        $this->assertSame($foo, $finalResult['thing']['thing:1']);
        $this->assertSame($bar, $finalResult['thing']['thing:2']);
    }

    /** @test */
    function including_previously_loaded_objects_in_the_result()
    {
        $foo = new Thing(1, 'Foo!');
        $result = Result::fromArray([], IdentityMap::with(['1' => $foo]))
            ->include('thing', '1', $foo);

        $this->assertSame($foo, $result['thing']['1']);
    }

    /** @test */
    function getting_previously_loaded_objects_from_the_result()
    {
        $foo = new Thing(1, 'Foo!');
        $result = Result::fromArray([], IdentityMap::with(['1' => $foo]));

        $this->assertSame($foo, $result->get(Thing::class, '1'));
    }

    /** @test */
    function cannot_alter_the_result_after_is_has_been_produced()
    {
        $result = Result::fromArray([]);

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
                '1' => $thing,
            ]
        ], IdentityMap::with([
            '1' => $thing,
        ]));

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Altering the results is not allowed.');

        unset($result['thing']);
    }
}

