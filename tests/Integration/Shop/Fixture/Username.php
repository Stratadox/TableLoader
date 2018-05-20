<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Fixture;

use function is_null;

final class Username
{
    private $name;
    private $lastName;

    public function __construct(string $name, string $lastName = null)
    {
        $this->name = $name;
        $this->lastName = $lastName;
    }

    public function firstName(): string
    {
        return $this->name;
    }

    public function lastName(): ?string
    {
        return $this->lastName;
    }

    public function __toString(): string
    {
        if (is_null($this->lastName)) {
            return $this->name;
        }
        return "{$this->name} {$this->lastName}";
    }
}
