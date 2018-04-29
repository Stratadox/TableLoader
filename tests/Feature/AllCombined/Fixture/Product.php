<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\AllCombined\Fixture;

final class Product extends Sellable
{
    private $reasonsToBuy;

    public function __construct(
        string $name,
        Prices $prices,
        ReasonsToBuy $reasonsToBuy
    ) {
        parent::__construct($name, $prices);
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
