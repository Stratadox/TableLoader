<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

/**
 * Converts a joined table into related objects.
 *
 * @author Stratadox
 */
final class JoinedTable implements LoadsTable
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
     * @return JoinedTable                The joined table converter.
     */
    public static function converter(
        MakesObjects $makesObjects,
        WiresObjects $relationships
    ): self {
        return new self($makesObjects, $relationships);
    }

    /** @inheritdoc */
    public function from(array $input): array
    {
        $objects = $this->makeObjects->from($input);
        $this->relationships->wire($objects, $input);
        return $objects;
    }
}
