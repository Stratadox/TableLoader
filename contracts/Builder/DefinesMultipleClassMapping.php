<?php

namespace Stratadox\TableLoader\Builder;

/**
 * Defines how to map an object for which the class is decided by the input.
 *
 * @author Stratadox
 */
interface DefinesMultipleClassMapping extends DefinesObjectMapping
{
    /**
     * Define which columns to use in identifying this entity.
     *
     * @param string ...$columns The columns to use in the identification of
     *                           the entity.
     * @return self|static       The multiple class mapping definition.
     */
    public function by(string ...$columns): DefinesMultipleClassMapping;

    /**
     * Define the available concrete class mappings to choose from.
     *
     * @param string             $key        The key to decide on.
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
     * @param array $properties The map of property instructions to apply to
     *                          each choice.
     * @return self|static      The multiple class mapping definition.
     */
    public function with(array $properties): DefinesMultipleClassMapping;
}
