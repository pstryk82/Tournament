<?php

namespace Pstryk82\LeagueBundle\Event;

use Pstryk82\LeagueBundle\Domain\Aggregate\Competition;

class ParticipantHasDrawn extends AbstractParticipantHasFinishedGame
{
    protected $eventName = 'pstryk82.competition.event.participant_has_drawn';
    /**
     * @var int
     */
    protected $drawScore;

    /**
     *
     * @param Competition $competition
     * @param string $aggregateId
     * @param int $drawScore
     */
    public function __construct(Competition $competition, $aggregateId, $drawScore)
    {
        parent::__construct($competition);
        $this->aggregateId = $aggregateId;
        $this->drawScore = $drawScore;
    }

    /**
     * @return int
     */
    public function getDrawScore()
    {
        return $this->drawScore;
    }
}
