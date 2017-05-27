<?php

namespace Pstryk82\LeagueBundle\Event;

use Pstryk82\LeagueBundle\Domain\Aggregate\Competition;
use Pstryk82\LeagueBundle\Domain\Logic\GameOutcomeResolver;

class ParticipantHasLost extends AbstractParticipantHasNotDrawn
{
    protected $eventName = 'pstryk82.competition.event.participant_has_lost';

    /**
     * @param Competition $competition
     * @param GameOutcomeResolver $gameOutcomeResolver
     */
    public function __construct(Competition $competition, GameOutcomeResolver $gameOutcomeResolver)
    {
        parent::__construct($competition, $gameOutcomeResolver);
        $this->aggregateId = $this->gameOutcomeResolver->getLoser()->getAggregateId();
    }
}
