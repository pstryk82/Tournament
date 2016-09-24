<?php

namespace Pstryk82\LeagueBundle\Event;

class TeamWasCreated extends AbstractEvent
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $rank;

    /**
     * @var string
     */
    private $stadium;

    public function __construct($aggregateId, $name, $rank, $stadium, \DateTime $happenedAt)
    {
        $this->aggregateId = $aggregateId;
        $this->name = $name;
        $this->rank = $rank;
        $this->stadium = $stadium;
        $this->happenedAt = $happenedAt;
    }

    public function getTeamId()
    {
        return $this->teamId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRank()
    {
        return $this->rank;
    }

    public function getStadium()
    {
        return $this->stadium;
    }
}
