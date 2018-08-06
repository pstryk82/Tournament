<?php

namespace Pstryk82\LeagueBundle\Domain\ReadModel\Projection;

use Doctrine\Common\Collections\ArrayCollection;

abstract class AbstractParticipantProjection
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var TeamProjection
     */
    private $team;

    /**
     * @var CompetitionProjection
     */
    private $competition;

    /**
     * @var ArrayCollection
     */
    protected $games;

    public function __construct($id)
    {
        $this->id = $id;
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
     * Set team.
     *
     * @param TeamProjection $team
     *
     * @return $this
     */
    public function setTeam(TeamProjection $team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team.
     *
     * @return TeamProjection
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @return CompetitionProjection
     */
    public function getCompetition()
    {
        return $this->competition;
    }

    /**
     * @param CompetitionProjection $competition
     *
     * @return $this
     */
    public function setCompetition(CompetitionProjection $competition)
    {
        $this->competition = $competition;

        return $this;
    }

//    /**
//     * @param Game $game
//     *
//     * @return $this
//     */
//    public function addGame(Game $game)
//    {
//        $this->games->add($game);
//
//        return $this;
//    }
//
//    /**
//     * @param Game $game
//     *
//     * @return $this
//     */
//    public function removeGame(Game $game)
//    {
//        $this->games->removeElement($game);
//
//        return $this;
//    }
}
