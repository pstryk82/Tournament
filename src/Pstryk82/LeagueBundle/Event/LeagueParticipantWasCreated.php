<?php

namespace Pstryk82\LeagueBundle\Event;

use Pstryk82\LeagueBundle\Domain\Aggregate\League;
use Pstryk82\LeagueBundle\Domain\Aggregate\Team;

class LeagueParticipantWasCreated extends AbstractEvent
{
    protected $eventName = 'pstryk82.league_participant.event.league_participant_was_created';
    /**
     * @var Team
     */
    private $team;

    /**
     * @var League
     */
    private $league;
    
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
    public function getTeam(): Team
    {
        return $this->team;
    }

    public function getLeague(): League
    {
        return $this->league;
    }
}
