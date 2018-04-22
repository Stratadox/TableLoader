<?php

namespace Stratadox\TableLoader;

/**
 * Identifies an entity in the row data.
 *
 * @author Stratadox
 */
interface IdentifiesEntities
{
    /**
     * Retrieves a string representation that can identify the entity.
     *
     * @param array $row            The data to identify.
     * @return string               A string representation of the identifier.
     * @throws CannotIdentifyEntity When identifying columns are missing.
     */
    public function for(array $row): string;
}
