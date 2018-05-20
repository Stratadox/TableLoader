<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Fixture;

use function sprintf;

final class Rating
{
    private const MINIMUM_SCORE = 0;
    private const MAXIMUM_SCORE = 5;

    private $score;

    private function __construct(int $score)
    {
        if ($score < Rating::MINIMUM_SCORE) {
            throw UnacceptableScore::cannotRateThisLow($score, Rating::MINIMUM_SCORE);
        }
        if ($score > Rating::MAXIMUM_SCORE) {
            throw UnacceptableScore::cannotRateThisHigh($score, Rating::MAXIMUM_SCORE);
        }
        $this->score = $score;
    }

    public static function give(int $score): self
    {
        return new Rating($score);
    }

    public function score(): int
    {
        return $this->score;
    }

    public function __toString(): string
    {
        return sprintf(
            '%d / %d',
            $this->score(),
            Rating::MAXIMUM_SCORE
        );
    }
}
