# Table Loader

[![Build Status](https://travis-ci.org/Stratadox/TableLoader.svg?branch=master)](https://travis-ci.org/Stratadox/TableLoader)
[![Coverage Status](https://coveralls.io/repos/github/Stratadox/TableLoader/badge.svg?branch=master)](https://coveralls.io/github/Stratadox/TableLoader?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Stratadox/TableLoader/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Stratadox/TableLoader/?branch=master)

## Installation

Install using `composer require stratadox/table-loader`

## Usage

### Sample 1

A unidirectional has-many mapping:

```php

$data = $this->table([
   //----------+-----------------+------------+---------------+,
    [ 'club_id', 'club_name'     , 'member_id', 'member_name' ],
   //----------+-----------------+------------+---------------+,
    [ 1        , 'Foo de la Club', 1          , 'Chuck Norris'],
    [ 1        , 'Foo de la Club', 2          , 'Jackie Chan' ],
    [ 2        , 'The Foo Bar'   , 1          , 'Chuck Norris'],
    [ 2        , 'The Foo Bar'   , 3          , 'John Doe'    ],
    [ 3        , 'Space Club'    , 4          , 'Captain Kirk'],
    [ 3        , 'Space Club'    , 5          , 'Darth Vader' ],
   //----------+-----------------+------------+---------------+,
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
    '1' => Club::establishedBy($chuckNorris, 'Foo de la Club'),
    '2' => Club::establishedBy($chuckNorris, 'The Foo Bar'),
    '3' => Club::establishedBy(Member::named('Captain Kirk'), 'Space Club'),
];
Member::named('Jackie Chan')->join($expectedClubs['1']);
Member::named('John Doe')->join($expectedClubs['2']);
Member::named('Darth Vader')->join($expectedClubs['3']);


assert($expectedClubs == $actualClubs);
```

### Sample 2

Bidirectional has-many mapping:

```php

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
