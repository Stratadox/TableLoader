<?php

namespace Stratadox\TableLoader;

use Stratadox\TableLoader\ContainsResultingObjects as Result;

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
     * @param Result           $objects List of objects as [label => [id => instance]].
     * @return array[]                  Relations as [$property => $relation].
     * @throws CannotLoadTable          When the entity could not be identified or hydrated.
     */
    public function load(
        KnowsWhereToLook $from,
        array $data,
        KnowsWhereToLookTo $to,
        Result $objects
    ): array;
}
