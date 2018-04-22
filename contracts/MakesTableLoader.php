<?php

namespace Stratadox\TableLoader;

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
     * @return LoadsTable The table-to-object mapper.
     * @throws CannotMakeTableMapping
     */
    public function __invoke(): LoadsTable;
}
