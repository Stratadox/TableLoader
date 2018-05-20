<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain;

use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Price\Prices;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason\AreAttributes;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason\AreFeatures;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason\ReasonsToBuy;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Review\Reviews;

final class Product extends Sellable
{
    private $reasonsToBuy;

    public function __construct(
        string $name,
        Prices $prices,
        Reviews $reviews,
        ReasonsToBuy $reasonsToBuy
    ) {
        parent::__construct($name, $prices, $reviews);
        $this->reasonsToBuy = $reasonsToBuy;
    }

    public function priceList(): string
    {
        return $this->prices->implode(PHP_EOL);
    }

    public function features(): ReasonsToBuy
    {
        return $this->reasonsToBuy->that(AreFeatures::ofTheProduct());
    }

    public function attributes(): ReasonsToBuy
    {
        return $this->reasonsToBuy->that(AreAttributes::ofTheProduct());
    }
}
