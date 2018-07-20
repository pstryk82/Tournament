<?php

namespace Pstryk82\LeagueBundle\Domain\ReadModel\Projection;

use Doctrine\Common\Collections\ArrayCollection;
use Pstryk82\LeagueBundle\Domain\Aggregate\AbstractParticipant;

class TeamProjection
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $rank;

    /**
     * @var string
     */
    private $stadium;

    /**
     * @var ArrayCollection
     */
    private $participants;

    public function __construct($id)
    {
        $this->id = $id;
        $this->participants = new ArrayCollection();
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
     * @return $this
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
     * @param int $rank
     *
     * @return $this
     */
    public function addRank($rank)
    {
        $this->rank += $rank;

        return $this;
    }

    /**
     * Get rank.
     *
     * @return int
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set stadium.
     *
     * @param string $stadium
     *
     * @return $this
     */
    public function setStadium($stadium)
    {
        $this->stadium = $stadium;

        return $this;
    }

    /**
     * Get stadium.
     *
     * @return string
     */
    public function getStadium()
    {
        return $this->stadium;
    }

    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @param AbstractParticipant $participant
     *
     * @return $this
     */
    public function addParticipant(AbstractParticipant $participant)
    {
        $this->participants->add($participant);

        return $this;
    }

    /**
     * @param AbstractParticipant $participant
     *
     * @return $this
     */
    public function removeParticipant(AbstractParticipant $participant)
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
