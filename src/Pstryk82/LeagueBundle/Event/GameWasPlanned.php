<?php

namespace Pstryk82\LeagueBundle\Event;

use Pstryk82\LeagueBundle\Domain\Aggregate\AbstractParticipant;
use Pstryk82\LeagueBundle\Domain\Aggregate\Competition;

class GameWasPlanned extends AbstractEvent
{
    /**
     * @var AbstractParticipant
     */
    private $homeParticipant;

    /**
     * @var AbstractParticipant
     */
    private $awayParticipant;

    /**
     * @var Competition
     */
    private $competition;

    /**
     * @var \DateTime
     */
    private $beginningTime;

    /**
     * @var bool
     */
    private $onNeutralGround;

    public function __construct($aggregateId, AbstractParticipant $homeParticipant, AbstractParticipant $awayParticipant, Competition $competition, \DateTime $beginningTime, $happenedAt, $onNeutralGround = false)
    {
        $this->aggregateId = $aggregateId;
        $this->homeParticipant = $homeParticipant;
        $this->awayParticipant = $awayParticipant;
        $this->competition = $competition;
        $this->beginningTime = $beginningTime;
        $this->happenedAt = $happenedAt;
        $this->onNeutralGround = $onNeutralGround;
    }

    public function getHomeParticipant()
    {
        return $this->homeParticipant;
    }

    public function getAwayParticipant()
    {
        return $this->awayParticipant;
    }

    public function getCompetition()
    {
        return $this->competition;
    }

    public function getBeginningTime()
    {
        return $this->beginningTime;
    }

    public function getOnNeutralGround()
    {
        return $this->onNeutralGround;
    }
}

