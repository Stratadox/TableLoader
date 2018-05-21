<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Infrastructure;

use Stratadox\IdentityMap\Ignore;
use Stratadox\IdentityMap\MapsObjectsByIdentity as Map;
use Stratadox\TableLoader\ContainsResultingObjects as ResultingObjects;
use Stratadox\TableLoader\LoadsTables;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Price\Money;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason\Feature;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason\NumberAttribute;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason\NumberValue;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason\TextAttribute;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason\TextListAttribute;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason\TextValue;

final class MappedLoader implements LoadsTables
{
    use Mapping;

    private $reviewData;

    private function __construct(array $reviewData)
    {
        $this->reviewData = $reviewData;
    }

    public static function withReviewData(array $reviewData): self
    {
        return new self($reviewData);
    }

    /** @inheritdoc */
    public function from(array $input, Map $identityMap = null): ResultingObjects
    {
        /**
         * @var LoadsTables          $make
         * @var ReviewsLoaderFactory $reviewLoader
         */
        [$make, $reviewLoader] = $this->tableLoader($this->reviewData);

        $map = Ignore::these(
            Money::class,
            Feature::class,
            NumberAttribute::class,
            TextAttribute::class,
            TextListAttribute::class,
            NumberValue::class,
            TextValue::class
        );

        $result = $make->from($input, $map);

        $reviewLoader->setIdentityMap($result->identityMap());

        return $result;
    }
}
