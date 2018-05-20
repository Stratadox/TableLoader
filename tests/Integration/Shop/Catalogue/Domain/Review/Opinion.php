<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Catalogue\Domain\Review;

use function strlen as theLengthOfThe;
use function substr as partOfThe;

final class Opinion
{
    private const MAX_SUMMARY_LENGTH = 13;

    private $summary;
    private $fullText;

    private function __construct(string $summary, string $fullText)
    {
        $this->summary = $summary;
        $this->fullText = $fullText;
    }

    public static function express(string $opinion): self
    {
        $summary = theLengthOfThe($opinion) < Opinion::MAX_SUMMARY_LENGTH
            ? $opinion
            : partOfThe($opinion, 0, Opinion::MAX_SUMMARY_LENGTH);
        return new self($summary, $opinion);
    }

    public function summary(): string
    {
        return $this->summary;
    }

    public function fullText(): string
    {
        return $this->fullText;
    }

    public function __toString(): string
    {
        return $this->summary();
    }
}
