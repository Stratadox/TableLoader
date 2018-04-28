<?php

namespace Stratadox\TableLoader;

use Stratadox\HydrationMapper\InstructsHowToMap;

interface DefinesSingleClassMapping extends DefinesObjectMapping
{
    public function by(string ...$columns): DefinesSingleClassMapping;

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
