<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Loader;

use PHPUnit\Framework\TestCase;
use Stratadox\TableLoader\Loader\Prefixed;

/**
 * @covers \Stratadox\TableLoader\Loader\Prefixed
 */
class Prefixed_fields_get_stripped_of_their_prefix_the_rest_is_skipped extends TestCase
{
    /** @test */
    function limiting_a_data_set_to_the_fields_with_the_right_prefix()
    {
        $data = [
            ['foo_id' => 1, 'foo_name' => 'Bar', 'ignore_this' => 'totally'],
            ['foo_id' => 2, 'foo_name' => 'Baz', 'ignore_this' => 'completely'],
            ['foo_id' => 3, 'foo_name' => 'Qux', 'ignore_this' => 'mercilessly'],
        ];

        $prefixed = Prefixed::with('foo');

        $this->assertSame([
            ['id' => 1, 'name' => 'Bar'],
            ['id' => 2, 'name' => 'Baz'],
            ['id' => 3, 'name' => 'Qux'],
        ], $prefixed->only($data));
    }

    /** @test */
    function limiting_a_data_set_using_a_custom_separator()
    {
        $data = [
            ['foo.id' => 1, 'foo.name' => 'Bar', 'ignore.this' => 'totally'],
            ['foo.id' => 2, 'foo.name' => 'Baz', 'ignore.this' => 'completely'],
            ['foo.id' => 3, 'foo.name' => 'Qux', 'ignore.this' => 'mercilessly'],
        ];

        $prefixed = Prefixed::with('foo', '.');

        $this->assertSame([
            ['id' => 1, 'name' => 'Bar'],
            ['id' => 2, 'name' => 'Baz'],
            ['id' => 3, 'name' => 'Qux'],
        ], $prefixed->only($data));
    }

    /** @test */
    function returning_an_empty_result_when_no_fields_match()
    {
        $data = [
            ['foo' => 'bar', 'baz' => 'qux'],
        ];

        $prefixed = Prefixed::with('foo');

        $this->assertEmpty($prefixed->only($data));
    }

    /** @test */
    function retrieving_the_label()
    {
        $prefixed = Prefixed::with('foo');
        $this->assertSame('foo', $prefixed->label());
    }
}
