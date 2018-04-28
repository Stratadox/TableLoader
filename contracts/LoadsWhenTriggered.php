<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

use Stratadox\HydrationMapper\InstructsHowToMap;
use Stratadox\HydrationMapper\InvalidMapperConfiguration;
use Stratadox\Hydrator\Hydrates;
use Stratadox\Instantiator\CannotInstantiateThis;

/**
 * Represents a decision to load a class when triggered by the decision value.
 *
 * @author Stratadox
 */
interface LoadsWhenTriggered
{
    /**
     * Defines what kind of object to map to.
     *
     * @param string              $class      The class to map to.
     * @param InstructsHowToMap[] $properties The mappings for the properties as
     *                                        [property => mappingInstruction]
     * @return self|static                    The decision mapping definition.
     */
    public function as(string $class, array $properties = []): self;

    /**
     * Define additional properties that apply to this choice.
     *
     * @param array $properties The map of property instructions to apply to
     *                          this choice.
     * @return self|static      The multiple class mapping definition.
     */
    public function with(array $properties): self;

    /**
     * Defines a has-one relationship that applies to this choice.
     *
     * @param string $property The property for this relationship.
     * @param string $label    The label of the related entities.
     * @return self|static     The decision mapping definition.
     */
    public function havingOne(
        string $property,
        string $label = null
    ): self;

    /**
     * Defines a has-many relationship that applies to this choice.
     *
     * @param string      $property        The property for this relationship.
     * @param string      $label           The label of the related entities.
     * @param string|null $collectionClass The class to contain the collection,
     *                                     or null for an array container.
     * @return self|static                 The decision mapping definition.
     */
    public function havingMany(
        string $property,
        string $label,
        string $collectionClass = null
    ): self;

    /**
     * Defines how to identify the related object
     *
     * @param string $label      The label of the related object.
     * @param string ...$columns The identifying columns for the relation.
     * @return self|static       The decision mapping definition.
     */
    public function identifying(string $label, string ...$columns): self;

    /**
     * Defines the label for the
     *
     * @param string $label
     * @return self|static  The decision mapping definition.
     */
    public function labeled(string $label): self;

    /**
     * Retrieves the decision trigger.
     *
     * @return string The value that triggers this decision.
     */
    public function decisionTrigger(): string;

    /**
     * Produce the hydrator for this decision.
     *
     * @return Hydrates The hydrator for this decision.
     *
     * @todo throw own exception
     * @throws InvalidMapperConfiguration For now.
     * @throws CannotInstantiateThis      For now.
     */
    public function hydrator(): Hydrates;

    /**
     * Returns the instructions on how to wire the objects.
     *
     * @return WiresObjects           The object wiring.
     * @throws CannotMakeTableMapping When mapping information is lacking.
     */
    public function wiring(): WiresObjects;
}