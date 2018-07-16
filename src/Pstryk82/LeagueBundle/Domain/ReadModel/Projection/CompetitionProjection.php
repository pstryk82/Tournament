<?php

namespace Pstryk82\LeagueBundle\Domain\ReadModel\Projection;

use Doctrine\Common\Collections\ArrayCollection;
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

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Competition
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set season.
     *
     * @param string $season
     *
     * @return Competition
     */
    public function setSeason($season)
    {
        $this->season = $season;

        return $this;
    }

    /**
     * Get season.
     *
     * @return string
     */
    public function getSeason()
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

    /**
     * @param AbstractParticipant $participant
     *
     * @return Competition
     */
    public function addParticipant(AbstractParticipant $participant)
    {
        $this->participants->add($participant);

        return $this;
    }

    /**
     * @param AbstractParticipant $participant
     *
     * @return Competition
     */
    public function removeParticipant(AbstractParticipant $participant)
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

    /**
     * @return $this
     */
    public function setRankPointsForWin($rankPointsForWin)
    {
        $this->rankPointsForWin = $rankPointsForWin;

        return $this;
    }

    /**
     * @return $this
     */
    public function setRankPointsForDraw($rankPointsForDraw)
    {
        $this->rankPointsForDraw = $rankPointsForDraw;

        return $this;
    }

    /**
     * @return $this
     */
    public function setRankPointsForLose($rankPointsForLose)
    {
        $this->rankPointsForLose = $rankPointsForLose;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getGames($round = null)
    {
        //$this->games->matching
        if (!is_null($round) && isset($this->games[$round])) {
            return $this->games->get($round);
        }
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
