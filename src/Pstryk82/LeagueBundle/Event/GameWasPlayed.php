<?php

namespace Pstryk82\LeagueBundle\Event;

use Pstryk82\LeagueBundle\Event\AbstractEvent;

class GameWasPlayed extends AbstractEvent
{
    protected $eventName = 'pstryk82.game.event.game_was_played';

    private $homeScore;
    private $awayScore;
    private $played;

    public function __construct($aggregateId, $homeScore, $awayScore, $happenedAt)
    {
        $this->aggregateId = $aggregateId;
        $this->homeScore = $homeScore;
        $this->awayScore = $awayScore;
        $this->happenedAt = $happenedAt;
        $this->played = true;
    }

    public function getAggregateId()
    {
        return $this->aggregateId;
    }

    public function getHomeScore()
    {
        return $this->homeScore;
    }

    public function getAwayScore()
    {
        return $this->awayScore;
    }

    public function getPlayed()
    {
        return $this->played;
    }
}
