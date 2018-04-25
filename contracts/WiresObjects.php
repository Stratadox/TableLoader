<?php

namespace Stratadox\TableLoader;

use Stratadox\TableLoader\ContainsResultingObjects as Result;

/**
 * Wires objects together.
 *
 * @author Stratadox
 */
interface WiresObjects
{
    /**
     * Wires objects together.
     *
     * @param Result $objects List of associative rows.
     * @param array  $data    List of objects as [label => [id => instance]].
     * @throws CannotLoadTable
     */
    public function wire(Result $objects, array $data): void;
}
