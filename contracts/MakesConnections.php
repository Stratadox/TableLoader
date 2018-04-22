<?php

namespace Stratadox\TableLoader;

/**
 * Loads relationships.
 *
 * @author Stratadox
 */
interface MakesConnections
{
    /**
     * Loads a relationship.
     *
     * @param KnowsWhereToLook $from    Where to map from.
     * @param array[]          $data    List of associative rows.
     * @param KnowsWhereToLook $to      Where to map to.
     * @param object[][]       $objects List of objects as [label => [id => instance]].
     * @return array[]                  Relations as [$property => $relation]
     * @throws CannotLoadTable
     */
    public function load(
        KnowsWhereToLook $from,
        array $data,
        KnowsWhereToLook $to,
        array $objects
    ): array;
}
