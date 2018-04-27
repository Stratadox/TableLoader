<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\OneToOne\WithInheritance\Fixture;

final class Admin extends User
{
    private $adminProfile;

    public function __construct(string $name, AdminProfile $profile)
    {
        $this->adminProfile = $profile;
        parent::__construct($name);
    }

    public function profile(): DescribesTheUser
    {
        return $this->adminProfile;
    }
}
