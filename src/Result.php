<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

use ArrayIterator;
use BadMethodCallException;
use Exception;
use Stratadox\IdentityMap\IdentityMap;
use Stratadox\IdentityMap\MapsObjectsByIdentity;
use Traversable;

/**
 * Result.
 *
 * @author Stratadox
 */
final class Result implements ContainsResultingObjects
{
    private $objects;
    private $identityMap;

    private function __construct(
        array $objects,
        MapsObjectsByIdentity $identityMap
    ) {
        $this->objects = $objects;
        $this->identityMap = $identityMap;
    }

    /**
     * Makes a new result from an array of objects as produced by table loaders
     * and an identity map.
     *
     * @param array[]               $objects     Array of objects, as:
     *                                           [label => [id => object]]
     * @param MapsObjectsByIdentity $identityMap The map of objects by class and
     *                                           identifier.
     * @return ContainsResultingObjects
     */
    public static function fromArray(
        array $objects,
        MapsObjectsByIdentity $identityMap = null
    ): ContainsResultingObjects {
        return new self($objects, $identityMap ?: IdentityMap::startEmpty());
    }

    /** @inheritdoc */
    public function has(string $class, string $id): bool
    {
        return $this->identityMap->has($class, $id);
    }

    /** @inheritdoc */
    public function get(string $class, string $id): object
    {
        return $this->identityMap->get($class, $id);
    }

    /** @inheritdoc */
    public function add(
        string $label,
        string $idForLoading,
        string $idForMap,
        object $object
    ): ContainsResultingObjects {
        return new self($this->merge(
            $this->objects,
            [$label => [$idForLoading => $object]]
        ), $this->identityMap->add($idForMap, $object));
    }

    /** @inheritdoc */
    public function mergeWith(
        ContainsResultingObjects $otherObjects
    ): ContainsResultingObjects {
        return new self(
            $this->merge($this->objects, $otherObjects),
            $otherObjects->identityMap()
        );
    }

    /** @inheritdoc */
    public function include(
        string $label,
        string $id,
        object $object
    ): ContainsResultingObjects {
        return new self($this->merge(
            $this->objects,
            [$label => [$id => $object]]
        ), $this->identityMap);
    }

    /**
     * @param object[][] $result
     * @param object[][] $with
     * @return array
     */
    private function merge(array $result, iterable $with): array
    {
        foreach ($with as $label => $objects) {
            foreach ($objects as $id => $object) {
                $result[$label][$id] = $object;
            }
        }
        return (array) $result;
    }

    /** @inheritdoc */
    public function identityMap(): MapsObjectsByIdentity
    {
        return $this->identityMap;
    }

    /** @inheritdoc */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->objects);
    }

    /** @inheritdoc */
    public function offsetExists($offset): bool
    {
        return isset($this->objects[$offset]);
    }

    /** @inheritdoc */
    public function offsetGet($offset): array
    {
        return $this->objects[$offset];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException('Altering the results is not allowed.');
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function offsetUnset($offset): void
    {
        throw new BadMethodCallException('Altering the results is not allowed.');
    }
}
