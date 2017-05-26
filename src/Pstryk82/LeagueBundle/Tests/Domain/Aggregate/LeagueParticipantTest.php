<?php

namespace Pstryk82\LeagueBundle\Tests\Domain\Aggregate;

use Pstryk82\LeagueBundle\Domain\Aggregate\Game;
use Pstryk82\LeagueBundle\Domain\Aggregate\League;
use Pstryk82\LeagueBundle\Domain\Aggregate\LeagueParticipant;
use Pstryk82\LeagueBundle\Domain\Aggregate\Team;
use Pstryk82\LeagueBundle\Domain\Logic\GameOutcomeResolver;
use Pstryk82\LeagueBundle\Event\LeagueParticipantWasCreated;
use Pstryk82\LeagueBundle\Event\ParticipantHasDrawn;
use Pstryk82\LeagueBundle\Event\ParticipantHasLost;
use Pstryk82\LeagueBundle\Event\ParticipantHasWon;

class LeagueParticipantTest extends AbstractDomainObjectTest
{
    /**
     * @var LeagueParticipant
     */
    private $participant;

    /**
     * @var Team | \PHPUnit_Framework_MokcObject_MOckObject
     */
    private $teamMock;

    /**
     *
     * @var GameOutcomeResolver | \PHPUnit_Framework_MokcObject_MOckObject
     */
    private $gameOutcomeResolverMock;

    /**
     * @var League | \PHPUnit_Framework_MokcObject_MOckObject
     */
    private $competitionMock;

    /**
     * @var Game | \PHPUnit_Framework_MokcObject_MOckObject
     */
    private $gameMock;

    public function setUp()
    {
        $this->teamMock = $this->getMockBuilder(Team::class)->disableOriginalConstructor()->getMock();
        $this->participant = new LeagueParticipant('league participant');
        $this->participant->setTeam($this->teamMock);
        $this->gameOutcomeResolverMock = $this->getMockBuilder(GameOutcomeResolver::class)->disableOriginalConstructor()->getMock();

        $this->competitionMock = $this->getMockBuilder(League::class)->disableOriginalConstructor()->getMock();

        $this->gameMock = $this->getMockBuilder(Game::class)->disableOriginalConstructor()->getMock();
        $this->gameMock->method('getCompetition')->willReturn($this->competitionMock);
    }

    public function tearDown()
    {
        unset(
            $this->participant,
            $this->teamMock,
            $this->gameOutcomeResolverMock,
            $this->competitionMock,
            $this->gameMock
        );
    }

    public function testCreate()
    {
        $this->participant = LeagueParticipant::create($this->teamMock, $this->competitionMock);

        $this->assertEquals($this->participant->getTeam(), $this->teamMock);
        $this->assertEquals($this->participant->getCompetition(), $this->competitionMock);

        $this->assertEventOnDomainObjectWasCreated($this->participant, LeagueParticipantWasCreated::class);
    }

    public function testRecordPointsForWin()
    {
        $this->gameOutcomeResolverMock->method('getWinner')->willReturn($this->participant);
        $this->gameOutcomeResolverMock->expects($this->once())->method('getWinnerScore');
        $this->gameOutcomeResolverMock->expects($this->once())->method('getLoserScore');

        $this->competitionMock->expects($this->once())->method('getPointsForWin');

        $this->participant->recordPointsForWin($this->gameMock, $this->gameOutcomeResolverMock);

        $this->assertEventOnDomainObjectWasCreated($this->participant, ParticipantHasWon::class);
    }

    public function testRecordPointsForLose()
    {
        $this->gameOutcomeResolverMock->method('getLoser')->willReturn($this->participant);
        $this->gameOutcomeResolverMock->expects($this->once())->method('getWinnerScore');
        $this->gameOutcomeResolverMock->expects($this->once())->method('getLoserScore');

        $this->competitionMock->expects($this->once())->method('getPointsForLose');

        $this->participant->recordPointsForLose($this->gameMock, $this->gameOutcomeResolverMock);
        $this->assertEventOnDomainObjectWasCreated($this->participant, ParticipantHasLost::class);
    }

    public function testRecordPointsForDraw()
    {
        $this->competitionMock->expects($this->once())->method('getPointsForDraw');

        $this->participant->recordPointsForDraw($this->gameMock, 2);

        $this->assertEventOnDomainObjectWasCreated($this->participant, ParticipantHasDrawn::class);
    }

}
