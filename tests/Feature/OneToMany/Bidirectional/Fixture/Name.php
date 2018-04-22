<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\OneToMany\Bidirectional\Fixture;

final class Name
{
    private $firstName;
    private $lastName;

    public function __construct(string $firstName, string $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function __toString()
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}
