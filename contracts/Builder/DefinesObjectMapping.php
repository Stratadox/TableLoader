<?php

namespace Stratadox\TableLoader\Builder;

use Stratadox\TableLoader\Loader\MakesObjects;
use Stratadox\TableLoader\Loader\WiresObjects;

/**
 * Defines how to map an object.
 *
 * @author Stratadox
 */
interface DefinesObjectMapping
{
    /**
     * Define which columns to use in identifying this entity.
     *
     * @param string ...$columns The columns to use in the identification of
     *                           the entity.
     * @return self|static       The object mapping definition.
     */
    public function by(string ...$columns);

    /**
     * Defines a has-one relationship.
     *
     * @param string $property The property for this relationship.
     * @param string $label    The label of the related entities.
     * @return self|static     The object mapping definition.
     */
    public function havingOne(
        string $property,
        string $label
    ): DefinesObjectMapping;

    /**
     * Defines a has-many relationship.
     *
     * @param string      $property        The property for this relationship.
     * @param string      $label           The label of the related entities.
     * @param string|null $collectionClass The class to contain the collection,
     *                                     or null for an array container.
     * @return self|static                 The object mapping definition.
     */
    public function havingMany(
        string $property,
        string $label,
        string $collectionClass = null
    ): DefinesObjectMapping;

    /**
     * Defines how to identify the related object
     *
     * @param string $label      The label of the related object.
     * @param string ...$columns The identifying columns for the relation.
     * @return self|static       The object mapping definition.
     */
    public function identifying(
        string $label,
        string ...$columns
    ): DefinesObjectMapping;

    /**
     * Returns the label of the entity.
     *
     * The label is used as prefix for the columns in joined tables, and as
     * offset in the result map.
     * Labels are also used to find the identity columns to use when loading the
     * relationships of the objects.
     *
     * @return string The label.
     * @see ContainsResultingObjects
     */
    public function label(): string;

    /**
     * Returns the columns used for identifying this entity.
     *
     * @return string[] The identification column(s).
     */
    public function identityColumns(): array;

    /**
     * Returns the instructions on how to produce the objects.
     *
     * @return MakesObjects           The object producer.
     * @throws CannotMakeTableMapping When mapping information is lacking.
     */
    public function objects(): MakesObjects;

    /**
     * Returns the instructions on how to wire the objects.
     *
     * @return WiresObjects           The object wiring.
     * @throws CannotMakeTableMapping When mapping information is lacking.
     */
    public function wiring(): WiresObjects;
}
