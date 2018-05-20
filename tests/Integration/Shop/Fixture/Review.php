<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Integration\Shop\Fixture;

final class Review
{
    private $author;
    private $rating;
    private $opinion;

    public function __construct(
        User $author,
        Rating $rating,
        Opinion $opinion
    ) {
        $this->author = $author;
        $this->rating = $rating;
        $this->opinion = $opinion;
    }

    public function author(): User
    {
        return $this->author;
    }

    public function rating(): Rating
    {
        return $this->rating;
    }

    public function opinion(): Opinion
    {
        return $this->opinion;
    }
}
