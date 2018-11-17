<?php

namespace Stratadox\TableLoader\Builder;

use Stratadox\HydrationMapper\InstructsHowToMap;

/**
 * Defines how to map an object for which the class is known in advance.
 *
 * @author Stratadox
 */
interface DefinesSingleClassMapping extends DefinesObjectMapping
{
    /**
     * Defines what kind of object to map to.
     *
     * @param string              $class      The class to map to.
     * @param InstructsHowToMap[] $properties The mappings for the properties as
     *                                        [property => mappingInstruction]
     * @return self|static                    The object mapping definition.
     */
    public function as(
        string $class,
        array $properties = []
    ): DefinesSingleClassMapping;
}
