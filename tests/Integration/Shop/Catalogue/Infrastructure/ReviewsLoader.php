<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Infrastructure;

use function array_values;
use function get_class;
use Stratadox\IdentityMap\IdentityMap;
use Stratadox\IdentityMap\MapsObjectsByIdentity;
use Stratadox\Proxy\Loader;
use Stratadox\TableLoader\Loader\LoadsTables;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Review\Reviews;

final class ReviewsLoader extends Loader
{
    /** @var LoadsTables */
    private $make;
    /** @var array[][] */
    private $data;
    /** @var MapsObjectsByIdentity */
    private $identityMap;
    /** @var ReviewsLoaderFactory */
    private $observer;

    public function __construct(
        object $forWhom,
        string $property,
        $position,
        LoadsTables $makeReviews,
        array $data,
        ReviewsLoaderFactory $observer
    ) {
        parent::__construct($forWhom, $property, $position);
        $this->data = $data;
        $this->make = $makeReviews;
        $this->identityMap = IdentityMap::startEmpty();
        $this->observer = $observer;
    }

    public function setIdentityMap(MapsObjectsByIdentity $identityMap): void
    {
        $this->identityMap = $identityMap;
    }

    protected function doLoad($object, string $property, $position = null): Reviews
    {
        $id = $this->identityMap->idOf($object);
        if (empty($this->data[get_class($object)][$id])) {
            return new Reviews;
        }
        $result = $this->make->from(
            $this->data[get_class($object)][$id],
            $this->identityMap
        );
        $this->observer->setIdentityMap($result->identityMap());
        return new Reviews(...array_values($result['review']));
    }
}
