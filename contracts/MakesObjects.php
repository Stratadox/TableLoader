<?php

namespace Stratadox\TableLoader;

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
     * @param array[] $input  Table-like data as list of maps.
     * @return object[]       List of objects as [label => [id => instance]].
     * @throws CannotLoadTable When the input could not be mapped.
     */
    public function from(array $input): array;
}
