<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Infrastructure;

use Stratadox\IdentityMap\IdentityMap;
use Stratadox\IdentityMap\MapsObjectsByIdentity as Map;
use Stratadox\IdentityMap\Whitelist;
use Stratadox\TableLoader\Loader\ContainsResultingObjects as ResultingObjects;
use Stratadox\TableLoader\Loader\LoadsTables;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Product;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Service;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\User\Admin;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\User\Customer;

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

        $map = Whitelist::forThe(
            $identityMap ?: IdentityMap::startEmpty(),
            Product::class,
            Service::class,
            Customer::class,
            Admin::class
        );

        $result = $make->from($input, $map);

        $reviewLoader->setIdentityMap($result->identityMap());

        return $result;
    }
}
