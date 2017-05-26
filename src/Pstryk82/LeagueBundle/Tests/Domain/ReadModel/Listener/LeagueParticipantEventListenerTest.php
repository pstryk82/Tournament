<?php

namespace Pstryk82\LeagueBundle\Tests\Domain\ReadModel\Listener;

use Pstryk82\LeagueBundle\Domain\Aggregate\Competition;
use Pstryk82\LeagueBundle\Domain\Aggregate\League;
use Pstryk82\LeagueBundle\Domain\Aggregate\LeagueParticipant;
use Pstryk82\LeagueBundle\Domain\Aggregate\Team;
use Pstryk82\LeagueBundle\Domain\Logic\GameOutcomeResolver;
use Pstryk82\LeagueBundle\Domain\ReadModel\Listener\LeagueParticipantEventListener;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\LeagueParticipantProjection;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\LeagueProjection;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\TeamProjection;
use Pstryk82\LeagueBundle\Event\LeagueParticipantWasCreated;
use Pstryk82\LeagueBundle\Event\ParticipantHasDrawn;
use Pstryk82\LeagueBundle\Event\ParticipantHasLost;
use Pstryk82\LeagueBundle\Event\ParticipantHasWon;

class LeagueParticipantEventListenerTest extends AbstractEventListnerTest
{
    /**
     * @var LeagueParticipantEventListener
     */
    private $listener;


    public function setup()
    {
        parent::setup();
        $this->listener = new LeagueParticipantEventListener($this->eventBusMock, $this->projectionStorageMock);
    }

    public function tearDown()
    {
        unset ($this->listener);
        parent::tearDown();
    }

    public function testOnLeagueParticipantWasCreated()
    {
        $teamProjection = new TeamProjection('teamId');
        $this->assertProjectionFound($teamProjection, TeamProjection::class, 0);
        $team = $this->getMockBuilder(Team::class)->disableOriginalConstructor()->getMock();
        $team->method('getAggregateId')->willReturn('teamId');
        $league = $this->getMockBuilder(League::class)->disableOriginalConstructor()->getMock();
        $league->method('getAggregateId')->willReturn('leagueId');
        $event = new LeagueParticipantWasCreated(
            'participant', $team, $league, $this->now
        );

        $competitionProjection = new LeagueProjection('leagueId');
        $this->assertProjectionFound($competitionProjection, LeagueProjection::class, 1);
        $competition = $this->getMockBuilder(Competition::class)->disableOriginalConstructor()->getMock();
        $competition->method('getAggregateId')->willReturn('leagueId');

        $leagueParticipant = new LeagueParticipantProjection('participant');
        $leagueParticipant->setTeam($teamProjection)->setCompetition($competitionProjection);

        $this->assertProjectionSaved($leagueParticipant, 2);

        $this->listener->when($event);
    }

    public function testOnParticipantHasWon()
    {
        $competition = $this->getMockBuilder(League::class)->disableOriginalConstructor()->getMock();
        $competition->method('getAggregateId')->willReturn('league');
        $competition->method('getPointsForWin')->willReturn(3);
        $participant = $this->getMockBuilder(LeagueParticipant::class)->disableOriginalConstructor()->getMock();
        $participant->method('getAggregateId')->willReturn('participant');
        $gameOutcomeResolver = $this->getMockBuilder(GameOutcomeResolver::class)->disableOriginalConstructor()->getMock();
        $gameOutcomeResolver->method('getWinner')->willReturn($participant);
        $gameOutcomeResolver->method('getWinnerScore')->willReturn(4);
        $gameOutcomeResolver->method('getLoserScore')->willReturn(1);
        $event = new ParticipantHasWon($competition, $gameOutcomeResolver);

        $participantProjection = new LeagueParticipantProjection('participant');
        $this->assertProjectionFound($participantProjection, LeagueParticipantProjection::class, 0);

        $leagueProjection = new LeagueProjection('league');
        $leagueProjection->setPointsForWin(5);
        $this->assertProjectionFound($leagueProjection, LeagueProjection::class, 1);

        $this->assertProjectionSaved($participantProjection, 2);
        $this->listener->when($event);

        $this->assertEquals(5, $participantProjection->getPoints());
        $this->assertEquals(4, $participantProjection->getGoalsFor());
        $this->assertEquals(1, $participantProjection->getGoalsAgainst());
        $this->assertEquals(3, $participantProjection->getGoalDifference());
    }

    public function testOnParticipantHasLost()
    {
        $competition = $this->getMockBuilder(League::class)->disableOriginalConstructor()->getMock();
        $competition->method('getAggregateId')->willReturn('league');
        $participant = $this->getMockBuilder(LeagueParticipant::class)->disableOriginalConstructor()->getMock();
        $participant->method('getAggregateId')->willReturn('participant');
        $gameOutcomeResolver = $this->getMockBuilder(GameOutcomeResolver::class)->disableOriginalConstructor()->getMock();
        $gameOutcomeResolver->method('getLoser')->willReturn($participant);
        $gameOutcomeResolver->method('getWinnerScore')->willReturn(4);
        $gameOutcomeResolver->method('getLoserScore')->willReturn(1);
        $event = new ParticipantHasLost($competition, $gameOutcomeResolver);

        $participantProjection = new LeagueParticipantProjection('participant');
        $this->assertProjectionFound($participantProjection, LeagueParticipantProjection::class, 0);

        $leagueProjection = new LeagueProjection('league');
        $leagueProjection->setPointsForLose(-1);
        $this->assertProjectionFound($leagueProjection, LeagueProjection::class, 1);

        $this->assertProjectionSaved($participantProjection, 2);
        $this->listener->when($event);

        $this->assertEquals(-1, $participantProjection->getPoints());
        $this->assertEquals(1, $participantProjection->getGoalsFor());
        $this->assertEquals(4, $participantProjection->getGoalsAgainst());
        $this->assertEquals(-3, $participantProjection->getGoalDifference());
    }

    public function testOnParticipantHasDrawn()
    {
        $competition = $this->getMockBuilder(League::class)->disableOriginalConstructor()->getMock();
        $competition->method('getAggregateId')->willReturn('league');
        $competition->method('getPointsForWin')->willReturn(3);
        $participant = $this->getMockBuilder(LeagueParticipant::class)->disableOriginalConstructor()->getMock();
        $participant->method('getAggregateId')->willReturn('participant');
        $event = new ParticipantHasDrawn($competition, 'participant', 2);

        $participantProjection = new LeagueParticipantProjection('participant');
        $this->assertProjectionFound($participantProjection, LeagueParticipantProjection::class, 0);

        $leagueProjection = new LeagueProjection('league');
        $leagueProjection->setPointsForDraw(2);
        $this->assertProjectionFound($leagueProjection, LeagueProjection::class, 1);

        $this->assertProjectionSaved($participantProjection, 2);
        $this->listener->when($event);

        $this->assertEquals(2, $participantProjection->getPoints());
        $this->assertEquals(2, $participantProjection->getGoalsFor());
        $this->assertEquals(2, $participantProjection->getGoalsAgainst());
        $this->assertEquals(0, $participantProjection->getGoalDifference());
    }
}
