<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\OneToOne\WithInheritance\Fixture;

abstract class Profile implements DescribesTheUser
{
    protected $about;

    public function __construct(string $about)
    {
        $this->about = $about;
    }

    public function about(): string
    {
        return $this->about;
    }
}
