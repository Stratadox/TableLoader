<?php

namespace Stratadox\TableLoader;

/**
 * Locates the data for a relationship.
 *
 * @author Stratadox
 */
interface KnowsWhereToLook
{
    /**
     * Retrieves the label of the referenced entity.
     *
     * @return string The label.
     */
    public function label(): string;

    /**
     * Identifies the relationship.
     *
     * @param array $relationship   The row containing the relationship.
     * @return string               The id of the related entity.
     * @throws CannotIdentifyEntity When the row does not contain the id.
     */
    public function this(array $relationship): string;
}
