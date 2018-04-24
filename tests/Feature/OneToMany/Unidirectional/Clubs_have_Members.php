<?php
declare(strict_types=1);

namespace Stratadox\TableLoader\Test\Feature\OneToMany\Unidirectional;

use function assert;
use PHPUnit\Framework\TestCase;
use Stratadox\Hydration\Mapper\Instruction\Is;
use Stratadox\TableLoader\Joined;
use Stratadox\TableLoader\Load;
use Stratadox\TableLoader\LoadsTable;
use Stratadox\TableLoader\Test\Feature\OneToMany\Unidirectional\Fixture\Club;
use Stratadox\TableLoader\Test\Feature\OneToMany\Unidirectional\Fixture\Member;
use Stratadox\TableLoader\Test\Feature\OneToMany\Unidirectional\Fixture\MemberList;
use Stratadox\TableLoader\Test\Helper\TableTransforming;

/**
 * @coversNothing
 */
class Clubs_have_Members extends TestCase
{
    use TableTransforming;

    /** @test */
    function loading_clubs_and_their_members_from_a_joined_table_result()
    {
        $data = $this->table([
            //---------+-----------------+------------+---------------+,
            [ 'club_id', 'club_name'     , 'member_id', 'member_name' ],
            //---------+-----------------+------------+---------------+,
            [ 1        , 'Foo de la Club', 1          , 'Chuck Norris'],
            [ 1        , 'Foo de la Club', 2          , 'Jackie Chan' ],
            [ 2        , 'The Foo Bar'   , 1          , 'Chuck Norris'],
            [ 2        , 'The Foo Bar'   , 3          , 'John Doe'    ],
            [ 3        , 'Space Club'    , 4          , 'Captain Kirk'],
            [ 3        , 'Space Club'    , 5          , 'Darth Vader' ],
            //---------+-----------------+------------+---------------+,
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

        assert($make instanceof LoadsTable);

        $objects = $make->from($data);
        /** @var Club[] $clubs */
        $clubs = $objects['club'];
        /** @var Member[] $members */
        $members = $objects['member'];

        $chuckNorris = $members['#1'];
        $jackieChan = $members['#2'];
        $johnDoe = $members['#3'];
        $jamesKirk = $members['#4'];
        $darthVader = $members['#5'];

        $fooDeLaClub = $clubs['#1'];
        $theFooBar = $clubs['#2'];
        $spaceClub = $clubs['#3'];

        $this->assertSame('Chuck Norris', $chuckNorris->name());
        $this->assertSame('Jackie Chan', $jackieChan->name());
        $this->assertSame('John Doe', $johnDoe->name());
        $this->assertSame('Captain Kirk', $jamesKirk->name());
        $this->assertSame('Darth Vader', $darthVader->name());

        $this->assertSame('Foo de la Club', $fooDeLaClub->name());
        $this->assertSame('The Foo Bar', $theFooBar->name());
        $this->assertSame('Space Club', $spaceClub->name());

        $this->assertSame($chuckNorris, $fooDeLaClub->foundingMember());
        $this->assertTrue($chuckNorris->isMemberOf($fooDeLaClub));
        $this->assertTrue($jackieChan->isMemberOf($fooDeLaClub));

        $this->assertSame($chuckNorris, $theFooBar->foundingMember());
        $this->assertTrue($chuckNorris->isMemberOf($theFooBar));
        $this->assertTrue($johnDoe->isMemberOf($theFooBar));

        $this->assertSame($jamesKirk, $spaceClub->foundingMember());
        $this->assertTrue($jamesKirk->isMemberOf($spaceClub));
        $this->assertTrue($darthVader->isMemberOf($spaceClub));

        $this->assertEquals(
            $this->expectedClubs(),
            $clubs
        );
    }

    private function expectedClubs(): array
    {
        $chuckNorris = Member::named('Chuck Norris');
        $club = [
            '#1' => Club::establishedBy($chuckNorris, 'Foo de la Club'),
            '#2' => Club::establishedBy($chuckNorris, 'The Foo Bar'),
            '#3' => Club::establishedBy(Member::named('Captain Kirk'), 'Space Club'),
        ];
        Member::named('Jackie Chan')->join($club['#1']);
        Member::named('John Doe')->join($club['#2']);
        Member::named('Darth Vader')->join($club['#3']);
        return $club;
    }
}
