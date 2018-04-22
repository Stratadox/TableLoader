<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\OneToMany\Bidirectional;

use function assert;
use PHPUnit\Framework\TestCase;
use Stratadox\Hydration\Mapper\Instruction\In;
use Stratadox\Hydration\Mapper\Instruction\Is;
use Stratadox\Hydration\Mapper\Instruction\Has;
use Stratadox\TableLoader\Joined;
use Stratadox\TableLoader\Load;
use Stratadox\TableLoader\LoadsTable;
use Stratadox\TableLoader\Test\Feature\OneToMany\Bidirectional\Fixture\Book;
use Stratadox\TableLoader\Test\Feature\OneToMany\Bidirectional\Fixture\Name;
use Stratadox\TableLoader\Test\Feature\OneToMany\Bidirectional\Fixture\Student;
use Stratadox\TableLoader\Test\Helper\TableTransforming;

/**
 * @coversNothing
 */
class Students_own_Books extends TestCase
{
    use TableTransforming;

    /** @test */
    function loading_students_and_their_books_from_a_joined_table_result()
    {
        $data = $this->table([
            //--------------------+--------------------+-----------------------+,
            [ 'student_first_name', 'student_last_name', 'book_name'           ],
            //--------------------+--------------------+-----------------------+,
            [ 'Alice'             , 'of Wonderland'    , 'Catching rabbits'    ],
            [ 'Alice'             , 'of Wonderland'    , 'Hacking 101'         ],
            [ 'Bob'               , 'the Builder'      , 'Toolset maintenance' ],
            //--------------------+--------------------+-----------------------+,
        ]);

        $make = Joined::table(
            Load::each('student')
                ->by('first_name', 'last_name')
                ->as(Student::class, [
                    'name' => Has::one(Name::class)
                        ->with('firstName', In::key('first_name'))
                        ->with('lastName', In::key('last_name'))
                ])
                ->havingMany('books', 'book'),
            Load::each('book')
                ->by('name')
                ->as(Book::class, ['name' => Is::string()])
                ->havingOne('owner', 'student')
        )();

        assert($make instanceof LoadsTable);

        $objects = $make->from($data);
        /** @var iterable|Student[] $students */
        $students = $objects['student'];
        /** @var iterable|Book[] $books */
        $books = $objects['book'];

        $this->assertCount(2, $students);
        $this->assertCount(3, $books);

        $alice = $students['Alice:of Wonderland'];
        $bob = $students['Bob:the Builder'];

        $catchingRabbits = $books['Catching rabbits'];
        $hacking101 = $books['Hacking 101'];
        $toolsetMaintenance = $books['Toolset maintenance'];

        $this->assertEquals('Alice of Wonderland', $alice->name());
        $this->assertEquals('Bob the Builder', $bob->name());

        $this->assertSame($catchingRabbits, $alice->books()[0]);
        $this->assertSame($hacking101, $alice->books()[1]);
        $this->assertSame($toolsetMaintenance, $bob->books()[0]);

        $this->assertSame($alice, $catchingRabbits->owner());
        $this->assertSame($alice, $hacking101->owner());
        $this->assertSame($bob, $toolsetMaintenance->owner());
    }
}
