<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Infrastructure;

use Stratadox\Hydration\Mapper\Instruction\Has;
use Stratadox\Hydration\Mapper\Instruction\In;
use Stratadox\Hydration\Mapper\Instruction\Is;
use Stratadox\TableLoader\Decide;
use Stratadox\TableLoader\InCase;
use Stratadox\TableLoader\Joined;
use Stratadox\TableLoader\Load;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Price\Money;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Price\Prices;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Product;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason\Feature;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason\NumberAttribute;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason\NumberValue;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason\ReasonsToBuy;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason\TextAttribute;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason\TextListAttribute;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Reason\TextValue;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Review\Opinion;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Review\Rating;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Review\Review;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Service;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\User\Admin;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\User\Customer;
use Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\User\Username;

trait Mapping
{
    private function tableLoader(array $reviewData): array
    {
        $reviewLoader = $this->reviewLoader($reviewData);
        return [Joined::table(
            Decide::which('item')->basedOn('type', ...[
                InCase::of('service')
                    ->as(Service::class, [
                        'description' => Is::stringInKey('text')
                    ]),
                InCase::of('product')
                    ->as(Product::class)
                    ->havingMany('reasonsToBuy', 'reason', ReasonsToBuy::class)
            ])->with([
                'name' => Is::string(),
                'reviews' => Has::many(Review::class)
                    ->containedInA(ReviewsProxy::class)
                    ->loadedBy($reviewLoader)
            ])->havingMany('prices', 'price', Prices::class),

            Load::each('price')->as(Money::class, [
                'currency' => In::key('iso'),
                'amount' => Is::int(),
            ])->by('iso', 'amount'),

            Decide::which('reason')->basedOn('type', ...[
                InCase::of('feature')->as(Feature::class),
                InCase::of('numeric')->as(NumberAttribute::class, [
                    'value' => Has::one(NumberValue::class)->with('value', Is::intInKey('int'))
                ]),
                InCase::of('text')->as(TextAttribute::class, [
                    'value' => Has::one(TextValue::class)->with('value', Is::stringInKey('x_text'))
                ]),
                InCase::of('text-list')->as(TextListAttribute::class)->havingMany('value', 'reason_x'),
            ])->with(['name' => Is::string()]),
            Load::each('reason_x')
                ->by('text')
                ->as(TextValue::class, ['value' => Is::stringInKey('text')])
        )(), $reviewLoader];
    }

    private function reviewLoader(array $reviewData): ReviewsLoaderFactory
    {
        return ReviewsLoaderFactory::withThe(
            Joined::table(
                Load::each('review')
                    ->as(Review::class, [
                        'rating' => Has::one(Rating::class)
                            ->with('score', Is::intInKey('rating')),
                        'opinion' => Has::one(Opinion::class)
                            ->with('summary')
                            ->with('fullText', In::key('full_text'))
                    ])
                    ->havingOne('author', 'user'),
                Decide::which('user')->basedOn('type', ...[
                    InCase::of('customer')->as(Customer::class),
                    InCase::of('admin')->as(Admin::class),
                ])->with(['name' => Has::one(Username::class)
                    ->with('name', Is::string())
                    ->with('lastName', Is::stringInKey('last_name')->nullable())
                ])
            )(),
            $reviewData
        );
    }
}
