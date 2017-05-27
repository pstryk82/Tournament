<?php

namespace Pstryk82\LeagueBundle\Tests\Domain\ReadModel\Listener;

use Pstryk82\LeagueBundle\Domain\Aggregate\AbstractParticipant;
use Pstryk82\LeagueBundle\Domain\Aggregate\Competition;
use Pstryk82\LeagueBundle\Domain\ReadModel\Listener\GameEventListener;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\AbstractParticipantProjection;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\CompetitionProjection;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\GameProjection;
use Pstryk82\LeagueBundle\Event\GameWasPlanned;
use Pstryk82\LeagueBundle\Event\GameWasPlayed;

class GameEventListenerTest extends AbstractEventListnerTest
{
    /**
     * @var GameEventListener
     */
    private $listener;

    public function setup()
    {
        parent::setup();
        $this->listener = new GameEventListener($this->projectionStorageMock);
    }

    public function tearDown()
    {
        unset ($this->listener);
        parent::tearDown();
    }

    public function testOnGameWasPlanned()
    {
        $homeParticipant = $this->getMockBuilder(AbstractParticipant::class)
            ->disableOriginalConstructor()->getMock();
        $homeParticipant->method('getAggregateId')->willReturn('home participant');
        $awayParticipant = $this->getMockBuilder(AbstractParticipant::class)
            ->disableOriginalConstructor()->getMock();
        $awayParticipant->method('getAggregateId')->willReturn('away participant');
        $competition = $this->getMockBuilder(Competition::class)
            ->disableOriginalConstructor()->getMock();
        $competition->method('getAggregateId')->willReturn('league');

        $event = new GameWasPlanned(
            'gameId',
            $homeParticipant,
            $awayParticipant,
            $competition,
            $this->now,
            $this->now,
            false
        );
        
        $homeParticipantProjection = $this->getMockBuilder(AbstractParticipantProjection::class)
            ->disableOriginalConstructor()->getMock();
        $awayParticipantProjection = $this->getMockBuilder(AbstractParticipantProjection::class)
            ->disableOriginalConstructor()->getMock();
        $competitionProjection = $this->getMockBuilder(CompetitionProjection::class)
            ->disableOriginalConstructor()->getMock();

        $gameProjection = new GameProjection('gameId');
        $gameProjection
            ->setHomeParticipant($homeParticipantProjection)
            ->setAwayParticipant($awayParticipantProjection)
            ->setCompetition($competitionProjection)
            ->setBeginningTime($this->now)
            ->setOnNeutralGround(false)
            ->setPlayed(false);

        $this->projectionStorageMock->expects($this->at(0))->method('find')->with('home participant', AbstractParticipantProjection::class)->willReturn($homeParticipantProjection);
        $this->projectionStorageMock->expects($this->at(1))->method('find')->with('away participant', AbstractParticipantProjection::class)->willReturn($awayParticipantProjection);
        $this->projectionStorageMock->expects($this->at(2))->method('find')->with('league', CompetitionProjection::class)->willReturn($competitionProjection);
        $this->projectionStorageMock->expects($this->at(3))->method('save')->with($gameProjection);

        $this->listener->when($event);
    }

    public function testOnGameWasPlayed()
    {
        $event = new GameWasPlayed('gameId', 2, 3, $this->now);
        $gameProjection = new GameProjection('gameId');
        $this->assertProjectionFound($gameProjection, GameProjection::class);

        $this->listener->when($event);

        $this->assertEquals(2, $gameProjection->getHomeScore());
        $this->assertEquals(3, $gameProjection->getAwayScore());
        $this->assertEquals(true, $gameProjection->getPlayed());
    }
}
