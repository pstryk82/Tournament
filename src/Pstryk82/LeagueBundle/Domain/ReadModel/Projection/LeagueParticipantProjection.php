<?php

namespace Pstryk82\LeagueBundle\Domain\ReadModel\Projection;

use Doctrine\Common\Collections\ArrayCollection;

class LeagueParticipantProjection extends AbstractParticipantProjection
{
    /**
     * @var int
     */
    private $points = 0;

    /**
     * @var int
     */
    private $goalsFor = 0;

    /**
     * @var int
     */
    private $goalsAgainst = 0;

    /**
     * @var int
     */
    private $goalDifference = 0;

    /**
     * @var int
     */
    private $gamesPlayed = 0;

    public function addPoints(int $points): self
    {
        $this->points += $points;

        return $this;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function addGoalsFor(int $goalsFor): self
    {
        $this->goalsFor += $goalsFor;

        return $this;
    }

    public function getGoalsFor(): int
    {
        return $this->goalsFor;
    }

    public function addGoalsAgainst(int $goalsAgainst): self
    {
        $this->goalsAgainst += $goalsAgainst;

        return $this;
    }

    public function getGoalsAgainst(): int
    {
        return $this->goalsAgainst;
    }

    public function addGoalDifference(int $goalDifference): self
    {
        $this->goalDifference += $goalDifference;

        return $this;
    }

    public function getGoalDifference(): int
    {
        return $this->goalDifference;
    }

    public function addGamesPlayed(int $gamesPlayed): self
    {
        $this->gamesPlayed += $gamesPlayed;

        return $this;
    }

    public function getGamesPlayed(): int
    {
        return $this->gamesPlayed;
    }

    public function getGames(): ArrayCollection
    {
        return $this->games;
    }
}
