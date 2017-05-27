<?php

namespace Pstryk82\LeagueBundle\Tests\Domain\ReadModel\Listener;

use Pstryk82\LeagueBundle\Domain\ReadModel\Listener\TeamEventListener;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\TeamProjection;
use Pstryk82\LeagueBundle\Event\TeamGainedRankPoints;
use Pstryk82\LeagueBundle\Event\TeamWasCreated;


class TeamEventListenerTest extends AbstractEventListnerTest
{
    /**
     * @var TeamEventListener
     */
    private $listener;


    public function setup()
    {
        parent::setup();
        $this->listener = new TeamEventListener($this->projectionStorageMock);
    }

    public function tearDown()
    {
        unset ($this->listener);
        parent::tearDown();
    }

    public function testOnTeamWasCreated()
    {
        $event = new TeamWasCreated('teamId', 'Borussia Dortmund', 123217, 'Signal Iduna Park', new \DateTime());

        $teamProjection = new TeamProjection('teamId');
        $teamProjection
            ->setName('Borussia Dortmund')
            ->addRank(123217)
            ->setStadium('Signal Iduna Park');

        $this->assertProjectionSaved($teamProjection);

        $this->listener->when($event);
    }

    public function testOnTeamGainedRankPoints()
    {
        $event = new TeamGainedRankPoints('teamId', 7, new \DateTime());
        $teamProjection = new TeamProjection('teamId');
        $teamProjection->addRank(100);

        $this->assertProjectionFound($teamProjection, TeamProjection::class, 0);
        $this->assertProjectionSaved($teamProjection, 1);

        $this->listener->when($event);

        $this->assertEquals(107, $teamProjection->getRank());
    }
}
