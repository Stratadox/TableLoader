<?php

namespace Stratadox\TableLoader\Loader;

use ArrayAccess;
use IteratorAggregate;
use Stratadox\IdentityMap\AlreadyThere;
use Stratadox\IdentityMap\MapsObjectsByIdentity;
use Stratadox\IdentityMap\NoSuchObject;
use Stratadox\TableLoader\Loader\ContainsResultingObjects as Result;

/**
 * Contains the resulting objects and the updated identity map.
 *
 * @author Stratadox
 */
interface ContainsResultingObjects extends ArrayAccess, IteratorAggregate
{
    /**
     * Returns the resulting identity map.
     *
     * @return MapsObjectsByIdentity The updated identity map.
     */
    public function identityMap(): MapsObjectsByIdentity;

    /**
     * Checks if the object is in the identity map.
     *
     * @param string $class The class of the object to check for.
     * @param string $id    The identity of the object, unique per class.
     * @return bool         Whether the object is in the map.
     */
    public function has(string $class, string $id): bool;

    /**
     * Gets the object from the identity map.
     *
     * @param string $class The class of the object to check for.
     * @param string $id    The identity of the object, unique per class.
     * @return object       The object that was stored in the map.
     * @throws NoSuchObject
     */
    public function get(string $class, string $id): object;

    /**
     * Adds the object to the result.
     *
     * @param string $label         The label of the object to add.
     * @param string $idForLoading  The id to use in loading the object.
     * @param string $idForMap      The id to use in the identity map.
     * @param object $object        The object that was stored in the map.
     * @return Result               A copy of the result with the object added.
     * @throws AlreadyThere         When the object was already in the map.
     */
    public function add(
        string $label,
        string $idForLoading,
        string $idForMap,
        object $object
    ): Result;

    /**
     * Include the object in the results.
     *
     * @param string $label  The label of the object to include in the results.
     * @param string $id     The id as used in loading the object.
     * @param object $object The object to include in the results.
     * @return Result        The updated results.
     */
    public function include(
        string $label,
        string $id,
        object $object
    ): Result;

    /**
     * Merge with other results.
     *
     * Note: The objects per label from both results are merged, but the identity map of the merged
     * result is copied without alteration.
     *
     * @param Result $objects The other results to merge with
     * @return Result         The updated results.
     */
    public function mergeWith(Result $objects): Result;
}
