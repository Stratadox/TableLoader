<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Loader;

use Stratadox\IdentityMap\MapsObjectsByIdentity as Map;
use Stratadox\ImmutableCollection\ImmutableCollection;

/**
 * Makes partially hydrated objects from an input array.
 *
 * This class is a composition of zero or more implementations of its own
 * interface. It Makes Objects by delegating the work to each of its
 * subcontractors, merging their work into one final result.
 *
 * @author Stratadox
 */
final class Extract extends ImmutableCollection implements MakesObjects
{
    private function __construct(MakesObjects ...$objects)
    {
        parent::__construct(...$objects);
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
    public function current(): MakesObjects
    {
        return parent::current();
    }

    /** @inheritdoc */
    public function from(array $input, Map $map): ContainsResultingObjects
    {
        $result = Result::fromArray([], $map);
        foreach ($this as $objects) {
            $result = $result->mergeWith($objects->from(
                $input,
                $result->identityMap()
            ));
        }
        return $result;
    }
}
