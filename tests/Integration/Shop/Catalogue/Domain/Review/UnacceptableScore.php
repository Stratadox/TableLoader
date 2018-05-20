<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Review;

use InvalidArgumentException;

final class UnacceptableScore extends InvalidArgumentException
{
    public static function cannotRateThisLow(int $score, int $minimum): self
    {
        return new UnacceptableScore(
            "Cannot accept the rating of $score, because it is less than $minimum."
        );
    }

    public static function cannotRateThisHigh(int $score, int $maximum): self
    {
        return new UnacceptableScore(
            "Cannot accept the rating of $score, because it is more than $maximum."
        );
    }
}
