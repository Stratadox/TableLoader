<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Loader;

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
     * @param string $property  The property to map.
     * @return MakesConnections The relationship connector.
     */
    public static function in(string $property): MakesConnections
    {
        return new self($property);
    }

    /** @inheritdoc */
    public function load(
        KnowsWhereToLook $from,
        array $data,
        KnowsWhereToLookTo $to,
        ContainsResultingObjects $objects
    ): array {
        // @todo add caching?
        $relations = [];
        foreach ($data as $relation) {
            $relations[
                $from->this($relation)
            ] = $objects[$to->label()][$to->this($relation)];
        }
        return [$this->property => $relations];
    }
}
