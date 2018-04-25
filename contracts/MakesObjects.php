<?php

namespace Stratadox\TableLoader;

use Stratadox\IdentityMap\MapsObjectsByIdentity as Map;
use Stratadox\TableLoader\ContainsResultingObjects as Result;

/**
 * Makes partially hydrated objects from an input array.
 *
 * @author Stratadox
 */
interface MakesObjects
{
    /**
     * Makes objects from the input array.
     *
     * @param array[] $input   Table-like data as list of maps.
     * @param Map     $map     The identity map with existing objects.
     * @return Result          The resulting objects.
     * @throws CannotLoadTable When the input could not be mapped.
     */
    public function from(array $input, Map $map): Result;
}
