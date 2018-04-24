<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\PreExistingEntities\Fixture;

final class Thing
{
    private $name;
    private $box;

    public function __construct(string $name, Box $box)
    {
        $this->name = $name;
        $this->box = $box;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function box(): Box
    {
        return $this->box;
    }
}
