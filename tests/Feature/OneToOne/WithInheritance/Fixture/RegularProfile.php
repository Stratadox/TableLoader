<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\OneToOne\WithInheritance\Fixture;

final class RegularProfile extends Profile
{
    public function about(): string
    {
        return 'Profile: ' . parent::about();
    }
}
