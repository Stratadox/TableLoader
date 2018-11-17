<?php

namespace Stratadox\TableLoader\Builder;

use Stratadox\HydrationMapper\InstructsHowToMap;

/**
 * Defines how to map an object for which the class is decided by the input.
 *
 * Primarily used for class structures that use inheritance. The class that
 * eventually gets instantiated is based on the value of one of the input keys.
 *
 * @author Stratadox
 */
interface DefinesMultipleClassMapping extends DefinesObjectMapping
{
    /**
     * Define the available concrete class mappings to choose from.
     *
     * The value of the decision key is compared with the trigger; when the
     * value matches the trigger, that trigger is used to produce the final
     * object.
     *
     * @param string             $key        The decision key to use.
     * @param LoadsWhenTriggered ...$choices The choices in concrete classes.
     * @return DefinesMultipleClassMapping   The multiple class mapping definition.
     */
    public function basedOn(
        string $key,
        LoadsWhenTriggered ...$choices
    ): DefinesMultipleClassMapping;

    /**
     * Define additional properties that apply to each of the choices.
     *
     * Property instructions are represented as a map (associative array) of
     * [string $propertyName => InstructsHowToMap $instruction]
     *
     * @param array $properties The map of property instructions to apply to
     *                          each choice.
     * @return self|static      The multiple class mapping definition.
     * @see InstructsHowToMap
     */
    public function with(array $properties): DefinesMultipleClassMapping;
}
