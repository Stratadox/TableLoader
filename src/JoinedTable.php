<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

use Stratadox\IdentityMap\IdentityMap;
use Stratadox\IdentityMap\MapsObjectsByIdentity;

/**
 * Converts a joined table into related objects.
 *
 * @author Stratadox
 */
final class JoinedTable implements LoadsTables
{
    private $makeObjects;
    private $relationships;

    private function __construct(
        MakesObjects $makeObjects,
        WiresObjects $relationships
    ) {
        $this->makeObjects = $makeObjects;
        $this->relationships = $relationships;
    }

    /**
     * Makes a new joined table converter.
     *
     * @param MakesObjects $makesObjects  The object extractor to use.
     * @param WiresObjects $relationships The relationship wiring.
     *
     * @return LoadsTables                The joined table converter.
     */
    public static function converter(
        MakesObjects $makesObjects,
        WiresObjects $relationships
    ): LoadsTables {
        return new self($makesObjects, $relationships);
    }

    /** @inheritdoc */
    public function from(
        array $input,
        MapsObjectsByIdentity $map = null
    ): ContainsResultingObjects {
        $map = $map ?: IdentityMap::startEmpty();
        $objects = $this->makeObjects->from($input, $map);
        $this->relationships->wire($objects, $input);
        return $objects;
    }
}
