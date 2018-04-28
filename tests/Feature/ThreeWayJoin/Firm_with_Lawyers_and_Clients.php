<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\ThreeWayJoin;

use function assert;
use PHPUnit\Framework\TestCase;
use Stratadox\TableLoader\Joined;
use Stratadox\TableLoader\Load;
use Stratadox\TableLoader\LoadsTables;
use Stratadox\TableLoader\Test\Feature\ThreeWayJoin\Fixture\Client;
use Stratadox\TableLoader\Test\Feature\ThreeWayJoin\Fixture\Firm;
use Stratadox\TableLoader\Test\Feature\ThreeWayJoin\Fixture\Lawyer;
use Stratadox\TableLoader\Test\Helper\TableTransforming;

/**
 * @coversNothing
 */
class Firm_with_Lawyers_and_Clients extends TestCase
{
    use TableTransforming;

    /** @test */
    function loading_firms_lawyers_and_clients_from_three_joined_tables()
    {
        $data = $this->table([
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

        assert($make instanceof LoadsTables);

        /** @var Firm[] $firms */
        $firms = $make->from($data)['firm'];

        $theFirm = $firms['The Firm'];
        $lawAndCo = $firms['Law & Co'];

        $this->assertCount(2, $theFirm->lawyers());
        $this->assertCount(1, $lawAndCo->lawyers());

        [$alice, $bob] = $theFirm->lawyers();
        [$charlie] = $lawAndCo->lawyers();

        $this->assertCount(3, $alice->clients());
        $this->assertCount(2, $bob->clients());
        $this->assertCount(2, $charlie->clients());

        [$johnDoe, $chuckNorris, $fooBar] = $alice->clients();

        $this->assertSame('John Doe', $johnDoe->name());
        $this->assertSame(10000, $johnDoe->value());

        $this->assertSame('Chuck Norris', $chuckNorris->name());
        $this->assertSame(9999999999999, $chuckNorris->value());

        $this->assertSame('Foo Bar', $fooBar->name());
        $this->assertSame(365667, $fooBar->value());


    }
}
