<?php

namespace Pstryk82\LeagueBundle\Event;

use Pstryk82\LeagueBundle\Domain\Aggregate\Competition;
use Pstryk82\LeagueBundle\Domain\Logic\GameOutcomeResolver;

class ParticipantHasWon extends AbstractParticipantHasNotDrawn
{
    protected $eventName = 'pstryk82.competition.event.participant_has_won';

    /**
     * @param Competition $competition
     * @param GameOutcomeResolver $gameOutcomeResolver
     */
    public function __construct(Competition $competition, GameOutcomeResolver $gameOutcomeResolver)
    {
        parent::__construct($competition, $gameOutcomeResolver);
        $this->aggregateId = $this->gameOutcomeResolver->getWinner()->getAggregateId();
    }
}