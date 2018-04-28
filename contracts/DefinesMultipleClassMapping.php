<?php

namespace Stratadox\TableLoader;

interface DefinesMultipleClassMapping extends DefinesObjectMapping
{
    public function by(string ...$columns): DefinesMultipleClassMapping;

    public function basedOn(
        string $key,
        LoadsWhenTriggered ...$choices
    ): DefinesMultipleClassMapping;

    public function with(array $properties): DefinesMultipleClassMapping;
}
