<?php

namespace Stratadox\TableLoader\Loader;

/**
 * Filters the input array.
 *
 * @author Stratadox
 */
interface FiltersTheArray
{
    /**
     * Limits the contents of the array.
     *
     * The array is expected to be "table-like", ie. a consist of a list of maps
     * where each map has the same set of keys.
     *
     * @param array $input The table-like array input to take a subset of.
     * @return array       The table-like subset of the table-like input.
     */
    public function only(array $input): array;

    /**
     * Retrieves the label for this filter.
     *
     * @return string The label of the filter.
     */
    public function label(): string;
}
