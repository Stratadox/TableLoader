# Table Loader

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

## What is this?

The `TableLoader` package is designed to transform the result of a select query into objects.
It solves the challenge of deserialising joined table rows into objects without duplicating the entities.

To *load* a table means to produce objects from the associative arrays that result from a SQL query.

An object that [`LoadsTables`](https://github.com/Stratadox/TableLoader/blob/master/contracts/LoadsTables.php)
can make interrelated objects from a list of associative arrays.

Table loading works closely together with the [Hydration](https://github.com/Stratadox/Hydrate) 
modules to easily integrate with mapped hydration and lazy- and extra lazy loading.

## What does it do?

The purpose of the `TableLoader` package is to transform SQL-like table results into a set of objects.

### Connecting eagerly loaded relationships

When eagerly loading a relationship from a SQL database, one generally performs some kind of `JOIN` query.

The `TableLoader` package provides several options for converting the joined result into interconnected objects.

Each entity can be given any number of `has-one` and/or `has-many` relationships.
Bidirectional associations can be produced by assigning such relationships to both sides.

Any number of tables can be joined at a time. Self-referencing joins are equally supported.

### Mapping concrete subclasses

When dealing with polymorphism in a SQL schema, objects are generally mapped in either of three ways:
- [Single Table Inheritance](https://martinfowler.com/eaaCatalog/singleTableInheritance.html)
- [Class Table Inheritance](https://martinfowler.com/eaaCatalog/classTableInheritance.html)
- [Concrete Table Inheritance](https://martinfowler.com/eaaCatalog/concreteTableInheritance.html)

The `TableLoader` supports any of these methods, so long as a `decision key` is provided. 
(Also known as `discriminator column`)

### Managing identities

It can happen that some of the objects have already been loaded by a previous query.

For example, let's assume we're first loading only employee `X`.
Later on we're fetching company `Y` with all employees - including employee `X`.

While we *do* want company `Y` to include employee `X` on the books, we *do not* want two copies
of employee `X` in memory.

To solve this challenge, loaded entities are added to an [Identity Map](https://github.com/Stratadox/IdentityMap).
The table loader consults the identity map when extracting an entity from the table row data.
A new entity is only produced if it was not already present in the map.

If employee `X` had a `lazy has-one` mapping to their company, the company relation was a [`Proxy`](https://github.com/Stratadox/Proxy)
when the employee was first loaded. 
By loading the company `Y`, and its `eager has-many` employees mapping, the real company `Y` is
automatically loaded into the relationship that previously held a proxy. 

## Usage Samples

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
        ->as(Club::class, ['name' => Is::string()])
        ->havingMany('memberList', 'member', MemberList::class),
    Load::each('member')
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

### Multiple joined tables:

```php
$data = table([
    //----------+--------------+---------------+---------------+,
    ['firm_name', 'lawyer_name', 'client_name' , 'client_value'],
    //----------+--------------+---------------+---------------+,
    ['The Firm' , 'Alice'      , 'John Doe'    , 10000         ],
    ['The Firm' , 'Bob'        , 'Jackie Chan' , 56557853526   ],
    ['The Firm' , 'Alice'      , 'Chuck Norris', 9999999999999 ],
    ['The Firm' , 'Bob'        , 'Alfred'      , 845478        ],
    ['Law & Co' , 'Charlie'    , 'Slender Man' , 95647467      ],
    ['The Firm' , 'Alice'      , 'Foo Bar'     , 365667        ],
    ['Law & Co' , 'Charlie'    , 'John Cena'   , 4697669670    ],
    //----------+--------------+---------------+---------------+,
]);

$make = Joined::table(
    Load::each('firm')->by('name')->as(Firm::class)->havingMany('lawyers', 'lawyer'),
    Load::each('lawyer')->by('name')->as(Lawyer::class)->havingMany('clients', 'client'),
    Load::each('client')->by('name')->as(Client::class)
)();

$firms = $make->from($data)['firm'];

$theFirm = $firms['The Firm'];
$lawAndCo = $firms['Law & Co'];

[$alice, $bob] = $theFirm->lawyers();
[$charlie] = $lawAndCo->lawyers();

assert(3 == count($alice->clients()));
assert(2 == count($bob->clients()));
assert(2 == count($charlie->clients()));
```

## To do

- Make simple table builder.
- Segregate builder interfaces.
- Allow direct hydrator injection in joined table builder?
- More unhappy path testing and better exception handling.
- Use deserializer instead of an old hydrator version.
