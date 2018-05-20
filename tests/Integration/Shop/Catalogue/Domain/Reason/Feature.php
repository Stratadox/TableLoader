<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason;

final class Feature implements ReasonToBuy
{
    private $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function named(string $name): ReasonToBuy
    {
        return new self($name);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
