<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Builder;

use Stratadox\ImmutableCollection\ImmutableCollection;
use Stratadox\TableLoader\Loader\Extract;
use Stratadox\TableLoader\Loader\JoinedTable;
use Stratadox\TableLoader\Loader\LoadsTables;
use Stratadox\TableLoader\Loader\Wired;

final class Joined extends ImmutableCollection implements MakesTableLoader
{
    public function __construct(DefinesObjectMapping ...$objectMappings)
    {
        parent::__construct(...$objectMappings);
    }

    /**
     * Makes a new builder for joined table mappers.
     *
     * @param DefinesObjectMapping ...$objectMappings
     * @return MakesTableLoader
     */
    public static function table(
        DefinesObjectMapping ...$objectMappings
    ): MakesTableLoader {
        return new self(...$objectMappings);
    }

    public function current(): DefinesObjectMapping
    {
        return parent::current();
    }

    /** @inheritdoc */
    public function __invoke(): LoadsTables
    {
        $wiring = [];
        $objects = [];
        $identity = [];
        foreach ($this as $mapping) {
            $identity[$mapping->label()] = $mapping->identityColumns();
        }
        foreach ($this as $mapping) {
            foreach ($identity as $label => $columns) {
                $mapping = $mapping->identifying($label, ...$columns);
            }
            $objects[] = $mapping->objects();
            $wiring[] = $mapping->wiring();
        }
        return JoinedTable::converter(
            Extract::these(...$objects),
            Wired::together(...$wiring)
        );
    }
}
