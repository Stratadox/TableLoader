<?php

namespace Stratadox\TableLoader;

/**
 * Maps table data to objects.
 *
 * @author Stratadox
 */
interface LoadsTable
{
    /**
     * Transforms table data into an object collection.
     *
     * @param string[][] $input A list of associative arrays.
     * @return object[][]       The collection of objects as
     *                          [label => [id => object]].
     * @throws CannotLoadTable   When the objects could not be constructed.
     */
    public function from(array $input): array;
}
