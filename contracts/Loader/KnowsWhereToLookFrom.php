<?php

namespace Stratadox\TableLoader\Loader;

/**
 * Locates the data for the relationship of a class.
 *
 * @author Stratadox
 */
interface KnowsWhereToLookFrom extends KnowsWhereToLook
{
    /**
     * Checks whether the relationship is applicable to this concrete class.
     *
     * @param object $shouldWeConnectIt The object we should maybe connect from.
     * @return bool                     Whether we should connect this object.
     */
    public function hereToo(object $shouldWeConnectIt): bool;
}
