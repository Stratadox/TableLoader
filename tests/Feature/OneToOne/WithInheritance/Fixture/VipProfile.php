<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\OneToOne\WithInheritance\Fixture;

final class VipProfile extends Profile
{
    public function about(): string
    {
        return 'VIP profile: ' . parent::about();
    }
}
