<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Unit\Fixture;

use function assert;

final class Child extends TheParent
{
    private $toy;

    public function __construct(string $name, Thing $toy = null)
    {
        parent::__construct($name);
        $this->toy = $toy;
    }

    public function toy(): Thing
    {
        assert($this->toy instanceof Thing);
        return $this->toy;
    }
}
