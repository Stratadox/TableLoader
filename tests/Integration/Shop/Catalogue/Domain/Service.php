<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain;

use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Price\Prices;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Review\Reviews;

final class Service extends Sellable
{
    private $description;

    public function __construct(
        string $name,
        string $description,
        Prices $prices,
        Reviews $reviews
    ) {
        parent::__construct($name, $prices, $reviews);
        $this->description = $description;
    }

    public function description(): string
    {
        return $this->description;
    }
}
