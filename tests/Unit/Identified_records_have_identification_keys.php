<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\TableLoader\CannotIdentifyEntity;
use Stratadox\TableLoader\Identified;

/**
 * @covers \Stratadox\TableLoader\Identified
 * @covers \Stratadox\TableLoader\MissingIdentifyingColumn
 */
class Identified_records_have_identification_keys extends TestCase
{
    /** @test */
    function computing_the_identifier_for_a_row()
    {
        $row = ['id' => 'foo'];

        $identifier = Identified::by('id');

        $this->assertSame('foo', $identifier->forLoading($row));
        $this->assertSame('foo', $identifier->forIdentityMap($row));
    }

    /** @test */
    function computing_the_identifier_from_multiple_columns()
    {
        $row = ['company_id' => 2, 'employee_id' => 14];

        $identifier = Identified::by('company_id', 'employee_id');

        $this->assertSame('2:14', $identifier->forLoading($row));
        $this->assertSame('2:14', $identifier->forIdentityMap($row));
    }

    /** @test */
    function computing_the_identifier_for_loading_the_instance()
    {
        $row = ['id' => 12, 'type' => 1];

        $identifier = Identified::by('id')->andForLoading('type');

        $this->assertSame('12:1', $identifier->forLoading($row));
    }

    /** @test */
    function computing_the_identifier_for_the_identity_map()
    {
        $row = ['id' => 12, 'type' => 1];

        $identifier = Identified::by('id')->andForLoading('type');

        $this->assertSame('12', $identifier->forIdentityMap($row));
    }

    /** @test */
    function differentiating_between_identifiers_for_loading_and_for_in_the_identity_map()
    {
        $identifier = Identified::by('first', 'second')->andForLoading('type');
        $row = ['first' => 1, 'second' => 2, 'type' => 'A'];

        $this->assertNotEquals(
            $identifier->forLoading($row),
            $identifier->forIdentityMap($row)
        );
        $this->assertSame('1:2:A', $identifier->forLoading($row));
        $this->assertSame('1:2', $identifier->forIdentityMap($row));
    }

    /** @test */
    function differentiating_between_similar_identifiers()
    {
        $identifier = Identified::by('first', 'second');

        $this->assertNotEquals(
            $identifier->forLoading(['first' => '_:_', 'second' => '_']),
            $identifier->forLoading(['first' => '_', 'second' => '_:_'])
        );
        $this->assertNotEquals(
            $identifier->forIdentityMap(['first' => '_:_', 'second' => '_']),
            $identifier->forIdentityMap(['first' => '_', 'second' => '_:_'])
        );
    }

    /** @test */
    function differentiating_between_very_similar_identifiers()
    {
        $identifier = Identified::by('first', 'second');

        $this->assertNotEquals(
            $identifier->forLoading(['first' => '_:_\\', 'second' => '_']),
            $identifier->forLoading(['first' => '_\\', 'second' => '_:_'])
        );
        $this->assertNotEquals(
            $identifier->forIdentityMap(['first' => '_:_\\', 'second' => '_']),
            $identifier->forIdentityMap(['first' => '_\\', 'second' => '_:_'])
        );
    }

    /** @test */
    function throwing_an_exception_when_the_identifying_field_is_missing()
    {
        $identifier = Identified::by('id');

        $this->expectException(CannotIdentifyEntity::class);
        $this->expectExceptionMessage(
            'Missing the identifying column `id` in the input data: []'
        );

        $identifier->forIdentityMap([]);
    }

    /** @test */
    function throwing_an_exception_when_one_of_the_identifying_fields_is_missing()
    {
        $identifier = Identified::by('foo', 'bar');

        $this->expectException(CannotIdentifyEntity::class);
        $this->expectExceptionMessage(
            'Missing the identifying column `bar` in the input data: {"foo":"bar"}'
        );

        $identifier->forIdentityMap(['foo' => 'bar']);
    }

    /** @test */
    function throwing_an_exception_when_the_identifying_field_is_missing_for_loading()
    {
        $identifier = Identified::by('id')->andForLoading('type');

        $this->expectException(CannotIdentifyEntity::class);
        $this->expectExceptionMessage(
            'Missing the identifying column `type` in the input data: {"id":1}'
        );

        $identifier->forLoading(['id' => 1]);
    }
}
