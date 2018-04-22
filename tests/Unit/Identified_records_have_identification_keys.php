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

        $this->assertSame('foo', $identifier->for($row));
    }

    /** @test */
    function computing_the_identifier_from_multiple_columns()
    {
        $row = ['company_id' => 2, 'employee_id' => 14];

        $identifier = Identified::by('company_id', 'employee_id');

        $this->assertSame('2:14', $identifier->for($row));
    }

    /** @test */
    function differentiating_between_similar_identifiers()
    {
        $identifier = Identified::by('first', 'second');

        $this->assertNotEquals(
            $identifier->for(['first' => '_:_', 'second' => '_']),
            $identifier->for(['first' => '_', 'second' => '_:_'])
        );
    }

    /** @test */
    function differentiating_between_very_similar_identifiers()
    {
        $identifier = Identified::by('first', 'second');

        $this->assertNotEquals(
            $identifier->for(['first' => '_:_\\', 'second' => '_']),
            $identifier->for(['first' => '_\\', 'second' => '_:_'])
        );
    }

    /** @test */
    function throwing_an_exception_when_the_identifying_field_is_missing()
    {
        $identifier = Identified::by('id');

        $this->expectException(CannotIdentifyEntity::class);
        $this->expectExceptionMessage('Missing the identifying column `id` in the input data: []');

        $identifier->for([]);
    }

    /** @test */
    function throwing_an_exception_when_one_of_the_identifying_fields_is_missing()
    {
        $identifier = Identified::by('foo', 'bar');

        $this->expectException(CannotIdentifyEntity::class);
        $this->expectExceptionMessage('Missing the identifying column `bar` in the input data: {"foo":"bar"}');

        $identifier->for(['foo' => 'bar']);
    }
}
