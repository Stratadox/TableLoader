<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

use BadMethodCallException;
use Exception;
use Stratadox\IdentityMap\MapsObjectsByIdentity;

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
        MapsObjectsByIdentity $identityMap
    ): ContainsResultingObjects {
        return new self($objects, $identityMap);
    }

    /** @inheritdoc */
    public function identityMap(): MapsObjectsByIdentity
    {
        return $this->identityMap;
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
