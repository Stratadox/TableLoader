<?php

namespace Stratadox\TableLoader;

/**
 * Locates the data for the relationship of a class.
 *
 * @author Stratadox
 */
interface KnowsWhereToLookTo extends KnowsWhereToLook
{
    /**
     * Checks whether we should ignore this row.
     *
     * @param array $row The data that might need ignoring.
     * @return bool      Whether we should ignore this row.
     */
    public function ignoreThe(array $row): bool;
}
