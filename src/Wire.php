<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

use Stratadox\TableLoader\ContainsResultingObjects as Result;

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
        KnowsWhereToLookFrom $from,
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
     * @param KnowsWhereToLookFrom $from     Identification for the source entity.
     * @param KnowsWhereToLook     $to       Identification for the target entity.
     * @param MakesConnections     $relation The type of relationship.
     * @return WiresObjects                  The wiring object.
     */
    public static function it(
        KnowsWhereToLookFrom $from,
        KnowsWhereToLook $to,
        MakesConnections $relation
    ): WiresObjects {
        return new Wire($from, $to, $relation);
    }

    /** @inheritdoc */
    public function wire(Result $objects, array $data): void
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
            if ($this->from->hereToo($objects[$id])) {
                $this->setter->call($objects[$id], $property, $relation);
            }
        }
    }
}
