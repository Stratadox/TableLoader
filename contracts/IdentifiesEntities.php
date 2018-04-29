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
     * Defines additional identification fields for during the loading process.
     *
     * @param string ...$columns
     * @return IdentifiesEntities
     */
    public function andForLoading(string ...$columns): IdentifiesEntities;

    /**
     * Retrieves a string representation that can identify the entity during the
     * loading process.
     *
     * @param array $row            The data to identify.
     * @return string               A string representation of the identifier.
     * @throws CannotIdentifyEntity When identifying columns are missing.
     */
    public function forLoading(array $row): string;

    /**
     * Retrieves a string representation that can identify the entity for in the
     * identity map.
     *
     * @param array $row            The data to identify.
     * @return string               A string representation of the identifier.
     * @throws CannotIdentifyEntity When identifying columns are missing.
     */
    public function forIdentityMap(array $row): string;

    /**
     * Checks whether all identifying columns in the row are null valued.
     *
     * @param array $row The data to identify.
     * @return bool      Whether the id for this row is null.
     */
    public function isNullFor(array $row): bool;
}
