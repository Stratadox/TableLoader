<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

/**
 * Wires a relationship together.
 *
 * @author Stratadox
 */
final class Wire implements WiresObjects
{
    private $from;
    private $to;
    private $relation;
    private $setter;

    private function __construct(
        KnowsWhereToLook $from,
        KnowsWhereToLook $to,
        MakesConnections $relation
    ) {
        $this->from = $from;
        $this->to = $to;
        $this->relation = $relation;
        $this->setter = function (string $property, $value): void {
            $this->$property = $value;
        };
    }

    /**
     * Makes a new object that wires a relationship together.
     *
     * @param KnowsWhereToLook $from     Identification for the source entity.
     * @param KnowsWhereToLook $to       Identification for the target entity.
     * @param MakesConnections $relation The type of relationship.
     * @return Wire                      The wiring object.
     */
    public static function it(
        KnowsWhereToLook $from,
        KnowsWhereToLook $to,
        MakesConnections $relation
    ): self {
        return new Wire($from, $to, $relation);
    }

    /** @inheritdoc */
    public function wire(array $objects, array $data): void
    {
        $relationships = $this->relation->load(
            $this->from,
            $data,
            $this->to,
            $objects
        );
        foreach ($relationships as $property => $relations) {
            $this->writeTo($objects[$this->from->label()], $property, $relations);
        }
    }

    private function writeTo(
        array $objects,
        string $property,
        array $relations
    ): void {
        foreach ($relations as $id => $relation) {
            $this->setter->call($objects[$id], $property, $relation);
        }
    }
}
