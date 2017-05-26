<?php

namespace Pstryk82\LeagueBundle\Event;

use Pstryk82\LeagueBundle\Domain\Aggregate\League;
use Pstryk82\LeagueBundle\Domain\Aggregate\Team;

class LeagueParticipantWasCreated extends AbstractEvent
{
    /**
     * @var Team
     */
    private $team;

    /**
     * @var League
     */
    private $league;
    
    /**
     * @param string $aggregateId
     * @param Team $team
     * @param string $leagueId
     */
    public function __construct($aggregateId, Team $team, League $league, $happenedAt)
    {
        $this->aggregateId = $aggregateId;
        $this->team = $team;
        $this->league = $league;
        $this->happenedAt = $happenedAt;
    }

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @return League
     */
    public function getLeague()
    {
        return $this->league;
    }
}
