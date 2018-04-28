<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

use function in_array;
use Stratadox\Hydrator\ArrayHydrator;
use Stratadox\Hydrator\CannotHydrate;
use Stratadox\Hydrator\Hydrates;

/**
 * Defines a has-many relationship.
 *
 * @author Stratadox
 */
final class HasMany implements MakesConnections
{
    private $property;
    private $collection;

    private function __construct(string $property, Hydrates $collection)
    {
        $this->property = $property;
        $this->collection = $collection;
    }

    /**
     * Makes a connector for a has-many type relationship.
     *
     * @param string        $property   The property to map.
     * @param Hydrates|null $collection The collection hydrator to use.
     * @return MakesConnections         The relationship connector.
     */
    public static function in(
        string $property,
        Hydrates $collection = null
    ): MakesConnections {
        return new self($property, $collection ?: ArrayHydrator::create());
    }

    /** @inheritdoc */
    public function load(
        KnowsWhereToLook $from,
        array $data,
        KnowsWhereToLook $to,
        ContainsResultingObjects $objects
    ): array {
        $related = [];
        foreach ($data as $row) {
            $object = $objects[$to->label()][$to->this($row)];
            if ($this->shouldAdd($object, $from->this($row), $related)) {
                $related[$from->this($row)][] = $object;
            }
        }
        return [$this->property => $this->makeCollections($from->label(), $related)];
    }

    /**
     * Checks if we should add the object to the relations collection.
     *
     * @param object $relation The related object.
     * @param string $from     The id of the object that holds the relationship.
     * @param array  $existing The map of existing relationships.
     * @return bool            Whether the object is new.
     */
    private function shouldAdd(
        object $relation,
        string $from,
        array $existing
    ): bool {
        return !isset($existing[$from])
            || !in_array($relation, $existing[$from], true);
    }

    /**
     * Makes collections for the relationship.
     *
     * @param string  $from The label of the entity we connect from.
     * @param array[] $many The objects to connect with.
     *
     * @return array        The collections as [string id => array|object collection]
     * @throws UnmappableRelationship
     */
    private function makeCollections(string $from, array $many): array
    {
        $collections = [];
        foreach ($many as $id => $relatedObjects) {
            try {
                $collections[$id] = $this->collection->fromArray($relatedObjects);
            } catch (CannotHydrate $exception) {
                throw UnmappableRelationship::encountered($exception, $this->property, $from, $id);
            }
        }
        return $collections;
    }
}
