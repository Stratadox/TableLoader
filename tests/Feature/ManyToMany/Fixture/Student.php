<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\ManyToMany\Fixture;

final class Student
{
    private $name;
    private $courses;

    public function __construct(string $name, Courses $courses)
    {
        $this->name = $name;
        $this->courses = $courses;
    }

    public static function signUp(string $name, Course ...$courses): self
    {
        return new self($name, new Courses(...$courses));
    }

    public function name(): string
    {
        return $this->name;
    }

    public function courses(): Courses
    {
        return $this->courses;
    }

    public function follows(Course $course): bool
    {
        return $this->courses->includes($course);
    }

    public function signUpFor(Course $course): void
    {
        $course->register($this);
        $this->courses = $this->courses->subscribeTo($course);
    }
}
