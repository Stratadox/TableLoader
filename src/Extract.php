<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

use Stratadox\IdentityMap\MapsObjectsByIdentity as Map;

/**
 * Makes partially hydrated objects from an input array.
 *
 * @author Stratadox
 */
final class Extract implements MakesObjects
{
    private $objects;

    private function __construct(MakesObjects ...$objects)
    {
        $this->objects = $objects;
    }

    /**
     * Makes a new object extractor that composes more object extractors.
     *
     * @param MakesObjects ...$objects The other object extractors.
     *
     * @return MakesObjects            The composed extractor.
     */
    public static function these(MakesObjects ...$objects): MakesObjects
    {
        return new self(...$objects);
    }

    /** @inheritdoc */
    public function from(array $input, Map $map): ContainsResultingObjects
    {
        $result = Result::fromArray([], $map);
        foreach ($this->objects as $objects) {
            $result = $result->mergeWith($objects->from(
                $input,
                $result->identityMap()
            ));
        }
        return $result;
    }
}
