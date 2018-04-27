<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\OneToOne\WithInheritance\Fixture;

use function sprintf;

final class AdminProfile implements DescribesTheUser
{
    private $level;
    private $about;

    public function __construct(string $about, int $level)
    {
        $this->level = $level;
        $this->about = $about;
    }

    public function about(): string
    {
        return sprintf(
            'Level %d admin profile: %s',
            $this->level,
            $this->about
        );
    }
}
