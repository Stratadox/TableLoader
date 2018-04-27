<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

final class OtherChild extends TheParent
{
    private $toys;

    public function __construct(string $name, Thing ...$toys)
    {
        parent::__construct($name);
        $this->toys = new Things(...$toys);
    }

    public function toys(): Things
    {
        return $this->toys;
    }
}
