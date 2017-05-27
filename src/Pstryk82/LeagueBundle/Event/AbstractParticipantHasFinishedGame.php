<?php

namespace Pstryk82\LeagueBundle\Event;

use Pstryk82\LeagueBundle\Domain\Aggregate\Competition;

abstract class AbstractParticipantHasFinishedGame extends AbstractEvent
{
    /**
     * @var Competition
     */
    protected $competition;

    /**
     * @param Competition $competition
     */
    public function __construct(Competition $competition)
    {
        $this->competition = $competition;
        $this->happenedAt = new \DateTime();
    }

    public function getCompetition()
    {
        return $this->competition;
    }
}
