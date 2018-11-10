<?php

namespace Stratadox\TableLoader\Builder;

use Stratadox\TableLoader\Loader\LoadsTables;

/**
 * Makes mappers that map table data to objects.
 *
 * @author Stratadox
 */
interface MakesTableLoader
{
    /**
     * Makes a table-to-object mapper.
     *
     * @return LoadsTables The table-to-object mapper.
     * @throws CannotMakeTableMapping
     */
    public function __invoke(): LoadsTables;
}
