<?php
declare(strict_types=1);

namespace Stratadox\TableLoader;

use Stratadox\ImmutableCollection\ImmutableCollection;

/**
 * Wires relationships together.
 *
 * @author Stratadox
 */
final class Wired extends ImmutableCollection implements WiresObjects
{
    /**
     * @param WiresObjects ...$relations The relations to wire together.
     */
    public function __construct(WiresObjects ...$relations)
    {
        parent::__construct(...$relations);
    }

    /**
     * Makes a new object that wires other objects together.
     *
     * @param WiresObjects ...$relations The relations to wire together.
     * @return Wired                     The wiring object.
     */
    public static function together(WiresObjects ...$relations): self
    {
        return new self(...$relations);
    }

    /**
     * Returns the current object wiring.
     *
     * @return WiresObjects
     */
    public function current(): WiresObjects
    {
        return parent::current();
    }

    /** @inheritdoc */
    public function wire(array $objects, array $usingTheData): void
    {
        foreach ($this as $relation) {
            $relation->wire($objects, $usingTheData);
        }
    }
}
