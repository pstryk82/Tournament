<?php

namespace Pstryk82\LeagueBundle\Tests\Domain\Aggregate;

use Pstryk82\LeagueBundle\Domain\Aggregate\Competition;
use Pstryk82\LeagueBundle\Domain\Aggregate\Game;
use Pstryk82\LeagueBundle\Domain\Aggregate\History\AggregateHistoryInterface;
use Pstryk82\LeagueBundle\Domain\Aggregate\LeagueParticipant;
use Pstryk82\LeagueBundle\Domain\Exception\GameLogicException;
use Pstryk82\LeagueBundle\Event\GameWasPlanned;
use Pstryk82\LeagueBundle\Event\GameWasPlayed;

class GameTest extends AbstractDomainObjectTest
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

    public function setUp()
    {
        $this->homeParticipant =
            $this->getMockBuilder(LeagueParticipant::class)->disableOriginalConstructor()->getMock();
        $this->homeParticipant->method('getAggregateId')->willReturn('home participant id');
        $this->awayParticipant =
            $this->getMockBuilder(LeagueParticipant::class)->disableOriginalConstructor()->getMock();
        $this->awayParticipant->method('getAggregateId')->willReturn('away participant id');
        $this->game = new Game('gameId');
        $this->game
            ->setHomeParticipant($this->homeParticipant)
            ->setAwayParticipant($this->awayParticipant);
    }

    public function tearDown()
    {
        unset($this->game, $this->homeParticipant, $this->awayParticipant);
    }

    public function testCreate()
    {
        $competition = $this->getMockBuilder(Competition::class)->disableOriginalConstructor()->getMock();
        $now = new \DateTIme();
        $this->game = Game::create($this->homeParticipant, $this->awayParticipant, $competition, $now, 1);

        $this->assertEquals($this->homeParticipant, $this->game->getHomeParticipant());
        $this->assertEquals($this->awayParticipant, $this->game->getAwayParticipant());
        $this->assertEquals($competition, $this->game->getCompetition());
        $this->assertEquals($now, $this->game->getBeginningTime());
        $this->assertFalse($this->game->getOnNeutralGround());

        $this->assertEventOnDomainObjectWasCreated($this->game, GameWasPlanned::class);
    }

    public function testCreateShouldFail()
    {
        $competition = $this->getMockBuilder(Competition::class)->disableOriginalConstructor()->getMock();
        $now = new \DateTIme();

        $this->expectException(GameLogicException::class);
        Game::create($this->homeParticipant, $this->homeParticipant, $competition, $now, 1);
    }

    public function testRecordResultDraw()
    {
        $this->homeParticipant->expects($this->once())->method('recordPointsForDraw');
        $this->awayParticipant->expects($this->once())->method('recordPointsForDraw');
        $this->game->recordResult(1, 1);

        $this->assertEquals(1, $this->game->getHomeScore());
        $this->assertEquals(1, $this->game->getAwayScore());
        $this->assertTrue($this->game->getPlayed());

        $this->assertEventOnDomainObjectWasCreated($this->game, GameWasPlayed::class);
    }

    public function testRecordResultNotDraw()
    {
        $this->homeParticipant->expects($this->once())->method('recordPointsForWin');
        $this->awayParticipant->expects($this->once())->method('recordPointsForLose');
        $this->game->recordResult(2, 0);

        $this->assertEquals(2, $this->game->getHomeScore());
        $this->assertEquals(0, $this->game->getAwayScore());
        $this->assertTrue($this->game->getPlayed());

        $this->assertEventOnDomainObjectWasCreated($this->game, GameWasPlayed::class);
    }
}
