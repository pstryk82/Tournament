<?php

namespace Pstryk82\LeagueBundle\Domain\ReadModel\Projection;

use Doctrine\Common\Collections\ArrayCollection;
use Pstryk82\LeagueBundle\Domain\Aggregate\AbstractParticipant;
use Pstryk82\LeagueBundle\Domain\Aggregate\Game;

abstract class CompetitionProjection
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $season;

    /**
     * @var ArrayCollection
     */
    protected $participants;

    /**
     * @var string
     */
    protected $discriminator;

    /**
     * @var int
     */
    protected $rankPointsForWin = 5;

    /**
     * @var int
     */
    protected $rankPointsForDraw = 2;

    /**
     * @var int
     */
    protected $rankPointsForLose = 0;

    /**
     * @var ArrayCollection
     */
    protected $games;

    public function __construct($id)
    {
        $this->id = $id;
        $this->participants = new ArrayCollection();
        $this->games = new ArrayCollection();
    }
    
    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setSeason(string $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getSeason(): string
    {
        return $this->season;
    }

    /**
     * @return ArrayCollection
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    public function addParticipant(AbstractParticipant $participant): self
    {
        $this->participants->add($participant);

        return $this;
    }

    public function removeParticipant(AbstractParticipant $participant): self
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    /**
     * @return int
     */
    public function getRankPointsForWin()
    {
        return $this->rankPointsForWin;
    }

    /**
     * @return int
     */
    public function getRankPointsForDraw()
    {
        return $this->rankPointsForDraw;
    }

    /**
     * @return int
     */
    public function getRankPointsForLose()
    {
        return $this->rankPointsForLose;
    }


    public function setRankPointsForWin($rankPointsForWin): self
    {
        $this->rankPointsForWin = $rankPointsForWin;

        return $this;
    }

    public function setRankPointsForDraw($rankPointsForDraw): self
    {
        $this->rankPointsForDraw = $rankPointsForDraw;

        return $this;
    }

    public function setRankPointsForLose($rankPointsForLose): self
    {
        $this->rankPointsForLose = $rankPointsForLose;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * @param Game $game
     *
     * @return $this
     */
    public function addGame(Game $game)
    {
        $this->games->add($game);

        return $this;
    }

    /**
     * @param Game $game
     *
     * @return $this
     */
    public function removeGame(Game $game)
    {
        $this->games->removeElement($game);

        return $this;
    }
}
