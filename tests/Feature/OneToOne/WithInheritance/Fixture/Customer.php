<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\OneToOne\WithInheritance\Fixture;

final class Customer extends User
{
    private $customerProfile;

    public function __construct(string $name, Profile $profile)
    {
        $this->customerProfile = $profile;
        parent::__construct($name);
    }

    public function profile(): DescribesTheUser
    {
        return $this->customerProfile;
    }
}
