<?php

namespace Stratadox\TableLoader;

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
     * @param array $objects  List of associative rows.
     * @param array $data     List of objects as [label => [id => instance]].
     * @throws CannotLoadTable
     */
    public function wire(array $objects, array $data): void;
}
