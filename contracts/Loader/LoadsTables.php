<?php

namespace Stratadox\TableLoader\Loader;

use Stratadox\IdentityMap\MapsObjectsByIdentity as Map;
use Stratadox\TableLoader\Loader\ContainsResultingObjects as ResultingObjects;

/**
 * Maps table data to objects.
 *
 * @author Stratadox
 */
interface LoadsTables
{
    /**
     * Transforms table data into an object collection.
     *
     * @param array[]    $input A list of associative arrays.
     * @param Map|null   $map   The map that contains entities by id.
     * @return ResultingObjects The collection of objects as
     *                          [label => [id => object]].
     * @throws CannotLoadTable   When the objects could not be constructed.
     */
    public function from(array $input, Map $identityMap = null): ResultingObjects;
}
