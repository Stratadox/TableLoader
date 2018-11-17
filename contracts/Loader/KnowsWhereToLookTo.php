<?php

namespace Stratadox\TableLoader\Loader;

/**
 * Locates the data for the relationship of a class.
 *
 * @author Stratadox
 */
interface KnowsWhereToLookTo extends KnowsWhereToLook
{
    /**
     * Checks whether we should ignore this row while loading the relationship.
     *
     * Mainly used to determine if the particular relationship was omitted from
     * the result set, for instance when a joined result contains only null
     * values for the particular joined table.
     *
     * @param array $row The data that might need ignoring.
     * @return bool      Whether we should ignore this row.
     */
    public function ignoreThe(array $row): bool;
}
