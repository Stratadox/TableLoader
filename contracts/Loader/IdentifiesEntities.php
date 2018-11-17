<?php

namespace Stratadox\TableLoader\Loader;

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
     * This is mainly used to be able to add the decision key as identifier in
     * the loading process, without having to use it as identifier in the
     * identity map.
     * The slight discrepancy between the identifiers is due to a different way
     * of indexing: when loading, objects are grouped by their label; this can
     * potentially contain an inheritance structure, in which case several
     * concrete classes share a label. In the identity map, object references
     * are stored based on their concrete class.
     * Since identifiers only need to be unique on a per-class level, it is
     * possible for two different objects with the same label to have the same
     * identifier in the identity map. In order to make them identifiable in the
     * result set, the decision key and value are also used as identifier when
     * assembling the result.
     *
     * @param string ...$columns
     * @return IdentifiesEntities
     */
    public function andForLoading(string ...$columns): IdentifiesEntities;

    /**
     * Retrieves a string that identifies the entity during the loading process.
     *
     * This is used to produce a unique identifier for the row, which serves as
     * index for the object that was loaded from this row.
     *
     * @param array $row            The data to identify.
     * @return string               A string representation of the identifier.
     * @throws CannotIdentifyEntity When identifying columns are missing.
     */
    public function forLoading(array $row): string;

    /**
     * Retrieves a string that identifies the entity for in the identity map.
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
