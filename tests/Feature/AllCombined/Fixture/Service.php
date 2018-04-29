<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\AllCombined\Fixture;

final class Service extends Sellable
{
    private $description;

    public function __construct(string $name, string $description, Prices $prices)
    {
        parent::__construct($name, $prices);
        $this->description = $description;
    }

    public function description(): string
    {
        return $this->description;
    }
}
