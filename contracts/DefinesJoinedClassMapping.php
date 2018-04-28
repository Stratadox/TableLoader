<?php

namespace Stratadox\TableLoader;

interface DefinesJoinedClassMapping extends DefinesObjectMapping
{
    public function by(string ...$columns): DefinesJoinedClassMapping;

    public function basedOn(
        string $key,
        LoadsWhenTriggered ...$choices
    ): DefinesJoinedClassMapping;

    public function with(array $properties): DefinesJoinedClassMapping;
}
