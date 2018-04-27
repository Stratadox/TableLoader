<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

/**
 * Defines a has-one relationship.
 *
 * @author Stratadox
 */
final class HasOne implements MakesConnections
{
    private $property;

    private function __construct(string $property)
    {
        $this->property = $property;
    }

    /**
     * Makes a connector for a has-one type relationship.
     *
     * @param string $property The property to map.
     *
     * @return HasOne          The relationship connector.
     */
    public static function in(string $property): self
    {
        return new self($property);
    }

    /** @inheritdoc */
    public function load(
        KnowsWhereToLook $from,
        array $data,
        KnowsWhereToLook $to,
        ContainsResultingObjects $objects
    ): array {
        // @todo add caching?
        $relations = [];
        foreach ($data as $relation) {
            $relations[$from->this($relation)] = $objects[$to->label()][$to->this($relation)];
        }
        return [$this->property => $relations];
    }
}
