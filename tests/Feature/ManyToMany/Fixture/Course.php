<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\ManyToMany\Fixture;

final class Course
{
    private $name;
    private $subscribedStudents;

    public function __construct(string $name, Students $subscribedStudents)
    {
        $this->name = $name;
        $this->subscribedStudents = $subscribedStudents;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function subscribedStudents(): Students
    {
        return $this->subscribedStudents;
    }

    public function register(Student $student): void
    {
        $this->subscribedStudents = $this->subscribedStudents->register($student);
    }
}
