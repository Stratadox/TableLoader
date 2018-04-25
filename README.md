 Table Loader

[![Build Status](https://travis-ci.org/Stratadox/TableLoader.svg?branch=master)](https://travis-ci.org/Stratadox/TableLoader)
[![Coverage Status](https://coveralls.io/repos/github/Stratadox/TableLoader/badge.svg?branch=master)](https://coveralls.io/github/Stratadox/TableLoader?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Stratadox/TableLoader/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Stratadox/TableLoader/?branch=master)
[![Infection Minimum](https://img.shields.io/badge/msi-100-brightgreen.svg)](https://travis-ci.org/Stratadox/TableLoader)
[![PhpStan Level](https://img.shields.io/badge/phpstan-7/7-brightgreen.svg)](https://travis-ci.org/Stratadox/TableLoader)
[![Maintainability](https://api.codeclimate.com/v1/badges/218a4f153cf26987ff55/maintainability)](https://codeclimate.com/github/Stratadox/TableLoader/maintainability)
[![Latest Stable Version](https://poser.pugx.org/stratadox/table-loader/v/stable)](https://packagist.org/packages/stratadox/table-loader)
[![License](https://poser.pugx.org/stratadox/table-loader/license)](https://packagist.org/packages/stratadox/table-loader)

## Installation

Install using `composer require stratadox/table-loader`

## About

Transforms the output of sql select queries into graphs of related objects.

Enables eager loading from joined table results.

Closely works together with the [Hydration](https://github.com/Stratadox/Hydrate) modules
to easily integrate with mapped hydration and lazy- and extra lazy loading.

## Usage

### Simple result without (eager) relations:

```php
$data = table([
    //----------+-----------------+,
    [ 'id'      , 'name'          ],
    //----------+-----------------+,
    [  1        , 'foo'           ],
    [  2        , 'bar'           ],
    //----------+-----------------+,
]);

$make = SimpleTable::converter(
    'thing',
    SimpleHydrator::forThe(Thing::class),
    Identified::by('id')
);

$things = $make->from($data)['thing'];

assert($things['1']->equals(new Thing(1, 'foo')));
assert($things['2']->equals(new Thing(2, 'bar')));
```

Assuming for table:
```php
function table(array $table): array
{
    $keys = array_shift($table);
    $result = [];
    foreach ($table as $row) {
        $result[] = array_combine($keys, $row);
    }
    return $result;
}
```

### Unidirectional has-many mapping:

```php
$data = table([
    //----------+------------------+-------------+----------------+,
    [ 'club_id' , 'club_name'      , 'member_id' , 'member_name'  ],
    //----------+------------------+-------------+----------------+,
    [  1        , 'Kick-ass Club'  ,  1          , 'Chuck Norris' ],
    [  1        , 'Kick-ass Club'  ,  2          , 'Jackie Chan'  ],
    [  2        , 'The Foo Bar'    ,  1          , 'Chuck Norris' ],
    [  2        , 'The Foo Bar'    ,  3          , 'John Doe'     ],
    [  3        , 'Space Club'     ,  4          , 'Captain Kirk' ],
    [  3        , 'Space Club'     ,  5          , 'Darth Vader'  ],
    //----------+------------------+-------------+----------------+,
]);

$make = Joined::table(
    Load::each('club')
        ->by('id')
        ->as(Club::class, ['name' => Is::string()])
        ->havingMany('memberList', 'member', MemberList::class),
    Load::each('member')
        ->by('id')
        ->as(Member::class, ['name' => Is::string()])
)();

$actualClubs = $make->from($data)['club'];


$chuckNorris = Member::named('Chuck Norris');
$expectedClubs = [
    '1' => Club::establishedBy($chuckNorris, 'Kick-ass Club'),
    '2' => Club::establishedBy($chuckNorris, 'The Foo Bar'),
    '3' => Club::establishedBy(Member::named('Captain Kirk'), 'Space Club'),
];
Member::named('Jackie Chan')->join($expectedClubs['1']);
Member::named('John Doe')->join($expectedClubs['2']);
Member::named('Darth Vader')->join($expectedClubs['3']);


assert($expectedClubs == $actualClubs);
```

### Bidirectional has-many mapping:

```php
$data = table([
    //---------------------+---------------------+-----------------------+,
    [ 'student_first_name' , 'student_last_name' , 'book_name'           ],
    //---------------------+---------------------+-----------------------+,
    [ 'Alice'              , 'of Wonderland'     , 'Catching rabbits'    ],
    [ 'Alice'              , 'of Wonderland'     , 'Hacking 101'         ],
    [ 'Bob'                , 'the Builder'       , 'Toolset maintenance' ],
    //---------------------+---------------------+-----------------------+,
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
        ->as(Book::class)
        ->havingOne('owner', 'student')
)();

$objects = $make->from($data);
$student = $objects['student'];
$book = $objects['book'];

assert($student['Alice:of Wonderland']->hasThe($book['Catching rabbits']));
assert($book['Catching rabbits']->isOwnedBy($student['Alice:of Wonderland']));

assert($student['Bob:the Builder']->hasThe($book['Toolset maintenance']));
assert($book['Toolset maintenance']->isOwnedBy($student['Bob:the Builder']));

assert($student['Alice:of Wonderland']->name() instanceof Name);
assert('Alice of Wonderland' === (string) $student['Alice:of Wonderland']->name());
```

### Many-to-many relationship:

```php
$data = table([
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

$objects = $make->from($data);
$student = $objects['student'];
$course = $objects['course'];

assert($student['Alice']->follows($course['Catching rabbits']));
assert($student['Alice']->follows($course['Hacking 101']));
assert($student['Alice']->doesNotFollow($course['Toolset maintenance']));

assert($student['Bob']->doesNotFollow($course['Catching rabbits']));
assert($student['Bob']->follows($course['Hacking 101']));
assert($student['Bob']->follows($course['Toolset maintenance']));

assert(count($course['Catching rabbits']->subscribedStudents()) === 1);
assert(count($course['Hacking 101']->subscribedStudents()) === 2);
assert(count($course['Toolset maintenance']->subscribedStudents()) === 1);
```

## To do

- Self-referring join support.
- Deal with (/ignore) nulls.
- Three-way join support.
- Hydrator injection in joined table builder.
- More unhappy path testing.
- Single table inheritance in main entities.
- Use `id` as default identification column.
