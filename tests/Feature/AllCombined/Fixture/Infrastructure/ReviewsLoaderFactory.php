<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\AllCombined\Fixture\Infrastructure;

use Stratadox\IdentityMap\MapsObjectsByIdentity;
use Stratadox\Proxy\LoadsProxiedObjects;
use Stratadox\Proxy\ProducesProxyLoaders;
use Stratadox\TableLoader\LoadsTables;

final class ReviewsLoaderFactory implements ProducesProxyLoaders
{
    private $makeReviews;
    private $fromData;
    /** @var ReviewsLoader[] */
    private $loaders = [];

    public function __construct(LoadsTables $makeReviews, array $fromReviewData)
    {
        $this->makeReviews = $makeReviews;
        $this->fromData = $fromReviewData;
    }

    public static function withThe(
        LoadsTables $makeReviews,
        array $fromReviewData
    ): self {
        return new self($makeReviews, $fromReviewData);
    }

    public function makeLoaderFor(
        $theOwner,
        string $ofTheProperty,
        $atPosition = null
    ): LoadsProxiedObjects {
        $loader = new ReviewsLoader(
            $theOwner,
            $ofTheProperty,
            $atPosition,
            $this->makeReviews,
            $this->fromData,
            $this
        );
        $this->loaders[] = $loader;
        return $loader;
    }

    public function setIdentityMap(MapsObjectsByIdentity $identityMap): void
    {
        foreach ($this->loaders as $loader) {
            $loader->setIdentityMap($identityMap);
        }
    }
}
