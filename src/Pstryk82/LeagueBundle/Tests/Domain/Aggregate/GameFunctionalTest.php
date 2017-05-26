<?php

namespace Pstryk82\LeagueBundle\Tests\Domain\Aggregate;

use Pstryk82\LeagueBundle\Domain\Aggregate\Game;
use Pstryk82\LeagueBundle\Domain\Aggregate\History\AggregateHistoryInterface;
use Pstryk82\LeagueBundle\Domain\Aggregate\League;
use Pstryk82\LeagueBundle\Domain\Aggregate\LeagueParticipant;
use Pstryk82\LeagueBundle\Domain\Aggregate\Team;
use Pstryk82\LeagueBundle\Event\GameWasPlanned;
use Pstryk82\LeagueBundle\Event\GameWasPlayed;

class GameFunctionalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Game
     */
    private $game;

    /**
     * @var LeagueParticipant
     */
    private $homeParticipant;

    /**
     * @var LeagueParticipant
     */
    private $awayParticipant;

    /**
     * @var Team
     */
    private $homeTeam;

    /**
     * @var Team
     */
    private $awayTeam;

    /**
     * @var League
     */
    private $competition;

    /**
     * @var \DateTime
     */
    private $now;

    public function setUp()
    {
        $this->now = new \DateTime();
        $this->competition = League::create(
                'league', '2016', 7, 3, -1, 3, 1, 0, 4
        );
        $this->homeTeam = Team::create('Chelsea', 1234, 'Stamford Bridge');
        $this->homeParticipant = LeagueParticipant::create($this->homeTeam, $this->competition);

        $this->awayTeam = Team::create('Legia', 1234, 'Stadion Wojska Polskiego');
        $this->awayParticipant = LeagueParticipant::create($this->awayTeam, $this->competition);
        $this->game = Game::create(
                $this->homeParticipant, $this->awayParticipant, $this->competition, $this->now
        );
    }

    public function tearDown()
    {
        unset(
            $this->game,
            $this->awayParticipant,
            $this->awayTeam,
            $this->homeParticipant,
            $this->homeTeam,
            $this->competition,
            $this->now
        );
    }

    public function testReconstituteFrom()
    {
        $history = $this->getMockBuilder(AggregateHistoryInterface::class)->getMock();
        $history->method('getAggregateId')->willReturn('gameId');
        $history->method('getEvents')->willReturn(
            [
                new GameWasPlanned(
                    $this->game->getAggregateId(), $this->homeParticipant, $this->awayParticipant, $this->competition, new \DateTime('2017-04-14'), $this->now
                ),
                new GameWasPlayed(
                    $this->game->getAggregateId(), 0, 1, new \DateTime('2017-04-14')
                ),
            ]
        );
        $this->game = Game::reconstituteFrom($history);

        $this->assertSame(0, $this->game->getHomeScore());
        $this->assertSame(1, $this->game->getAwayScore());
        $this->assertTrue($this->game->getPlayed());
        $this->assertEquals($this->homeParticipant, $this->game->getHomeParticipant());
        $this->assertEquals($this->awayParticipant, $this->game->getAwayParticipant());
    }

}
