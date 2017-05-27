<?php

namespace Pstryk82\LeagueBundle\Event;

class LeagueWasCreated extends AbstractEvent
{
    protected $eventName = 'pstryk82.league.event.league_was_created';
    private $name;
    private $season;
    private $rankPointsForWin;
    private $rankPointsForDraw;
    private $rankPointsForLose;
    private $pointsForWin;
    private $pointsForDraw;
    private $pointsForLose;
    private $numberOfLegs;

    /**
     * @param string $aggregateId
     * @param string $name
     * @param string $season
     * @param int $rankPointsForWin
     * @param int $rankPointsForDraw
     * @param int $rankPointsForLose
     * @param int $pointsForWin
     * @param int $pointsForDraw
     * @param int $pointsForLose
     * @param int $numberOfLegs
     * @param \DateTime $happenedAt
     */
    public function __construct($aggregateId, $name, $season, $rankPointsForWin, $rankPointsForDraw, $rankPointsForLose, $pointsForWin, $pointsForDraw, $pointsForLose, $numberOfLegs, $happenedAt)
    {
        $this->aggregateId = $aggregateId;
        $this->name = $name;
        $this->season = $season;
        $this->rankPointsForWin = $rankPointsForWin;
        $this->rankPointsForDraw = $rankPointsForDraw;
        $this->rankPointsForLose = $rankPointsForLose;
        $this->pointsForWin = $pointsForWin;
        $this->pointsForDraw = $pointsForDraw;
        $this->pointsForLose = $pointsForLose;
        $this->numberOfLegs = $numberOfLegs;
        $this->happenedAt = $happenedAt;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSeason()
    {
        return $this->season;
    }

    public function getRankPointsForWin()
    {
        return $this->rankPointsForWin;
    }

    public function getRankPointsForDraw()
    {
        return $this->rankPointsForDraw;
    }

    public function getRankPointsForLose()
    {
        return $this->rankPointsForLose;
    }

    public function getPointsForWin()
    {
        return $this->pointsForWin;
    }

    public function getPointsForDraw()
    {
        return $this->pointsForDraw;
    }

    public function getPointsForLose()
    {
        return $this->pointsForLose;
    }

    public function getNumberOfLegs()
    {
        return $this->numberOfLegs;
    }
}
