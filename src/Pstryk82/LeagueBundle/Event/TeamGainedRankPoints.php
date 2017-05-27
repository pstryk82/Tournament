<?php

namespace Pstryk82\LeagueBundle\Event;

class TeamGainedRankPoints extends AbstractEvent
{
    protected $eventName = 'pstryk82.team.event.team_gained_rank_points';

    /**
     * @var int
     */
    private $numberOfAddedRankPoints;
    
    public function __construct($teamId, $numberOfAddedRankPoints, \DateTime $happenedAt)
    {
        $this->aggregateId = $teamId;
        $this->numberOfAddedRankPoints = $numberOfAddedRankPoints;
        $this->happenedAt = $happenedAt;
    }

    public function getNumberOfAddedRankPoints()
    {
        return $this->numberOfAddedRankPoints;
    }
}
