<?php

namespace Pstryk82\LeagueBundle\Domain\ReadModel\Projection;

class GameProjection
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var bool
     */
    private $played;

    /**
     * @var AbstractParticipantProjection
     */
    private $homeParticipant;

    /**
     * @var AbstractParticipantProjection
     */
    private $awayParticipant;

    /**
     * @var int
     */
    private $homeScore;

    /**
     * @var int
     */
    private $awayScore;

    /**
     * @var CompetitionProjection
     */
    private $competition;

    /**
     * @var bool
     */
    private $onNeutralGround = false;

    /**
     * @var \DateTime
     */
    private $beginningTime;

    /**
     * @var string
     */
    private $round;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPlayed()
    {
        return $this->played;
    }

    public function getHomeParticipant()
    {
        return $this->homeParticipant;
    }

    public function getAwayParticipant()
    {
        return $this->awayParticipant;
    }

    public function getHomeScore()
    {
        return $this->homeScore;
    }

    public function getAwayScore()
    {
        return $this->awayScore;
    }

    public function getCompetition()
    {
        return $this->competition;
    }

    public function getOnNeutralGround()
    {
        return $this->onNeutralGround;
    }

    public function getBeginningTime()
    {
        return $this->beginningTime;
    }

    public function setPlayed($played)
    {
        $this->played = $played;
        return $this;
    }

    public function setHomeParticipant(AbstractParticipantProjection $homeParticipant)
    {
        $this->homeParticipant = $homeParticipant;
        return $this;
    }

    public function setAwayParticipant(AbstractParticipantProjection $awayParticipant)
    {
        $this->awayParticipant = $awayParticipant;
        return $this;
    }

    public function setHomeScore($homeScore)
    {
        $this->homeScore = $homeScore;
        return $this;
    }

    public function setAwayScore($awayScore)
    {
        $this->awayScore = $awayScore;
        return $this;
    }

    public function setCompetition(CompetitionProjection $competition)
    {
        $this->competition = $competition;
        return $this;
    }

    public function setOnNeutralGround($onNeutralGround)
    {
        $this->onNeutralGround = $onNeutralGround;
        return $this;
    }

    public function setBeginningTime(\DateTime $beginningTime)
    {
        $this->beginningTime = $beginningTime;
        return $this;
    }

    public function getRound(): string
    {
        return $this->round;
    }

    public function setRound($round): self
    {
        $this->round = $round;

        return $this;
    }



}
