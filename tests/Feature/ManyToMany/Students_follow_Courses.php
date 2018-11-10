<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\ManyToMany;

use function assert;
use PHPUnit\Framework\TestCase;
use Stratadox\TableLoader\Builder\Joined;
use Stratadox\TableLoader\Builder\Load;
use Stratadox\TableLoader\Loader\LoadsTables;
use Stratadox\TableLoader\Test\Feature\ManyToMany\Fixture\Course;
use Stratadox\TableLoader\Test\Feature\ManyToMany\Fixture\Courses;
use Stratadox\TableLoader\Test\Feature\ManyToMany\Fixture\Student;
use Stratadox\TableLoader\Test\Feature\ManyToMany\Fixture\Students;
use Stratadox\TableLoader\Test\Helper\TableTransforming;

/**
 * @coversNothing
 */
class Students_follow_Courses extends TestCase
{
    use TableTransforming;

    /** @test */
    function loading_students_and_their_books_from_a_joined_table_result()
    {
        $data = $this->table([
            //--------------+-----------------------+,
            [ 'student_name', 'course_name'         ],
            //--------------+-----------------------+,
            [ 'Alice'       , 'Catching rabbits'    ],
            [ 'Alice'       , 'Hacking 101'         ],
            [ 'Bob'         , 'Toolset maintenance' ],
            [ 'Bob'         , 'Hacking 101'         ],
            //--------------+-----------------------+,
        ]);

        $make = Joined::table(
            Load::each('student')
                ->by('name')
                ->as(Student::class)
                ->havingMany('courses', 'course', Courses::class),
            Load::each('course')
                ->by('name')
                ->as(Course::class)
                ->havingMany('subscribedStudents', 'student', Students::class)
        )();

        assert($make instanceof LoadsTables);

        $objects = $make->from($data);
        /** @var iterable|Student[] $student */
        $student = $objects['student'];
        /** @var iterable|Course[] $course */
        $course = $objects['course'];

        $this->assertTrue($student['Alice']->follows($course['Catching rabbits']));
        $this->assertTrue($student['Alice']->follows($course['Hacking 101']));
        $this->assertFalse($student['Alice']->follows($course['Toolset maintenance']));

        $this->assertFalse($student['Bob']->follows($course['Catching rabbits']));
        $this->assertTrue($student['Bob']->follows($course['Hacking 101']));
        $this->assertTrue($student['Bob']->follows($course['Toolset maintenance']));

        $this->assertCount(1, $course['Catching rabbits']->subscribedStudents());
        $this->assertCount(2, $course['Hacking 101']->subscribedStudents());
        $this->assertCount(1, $course['Toolset maintenance']->subscribedStudents());
    }
}
