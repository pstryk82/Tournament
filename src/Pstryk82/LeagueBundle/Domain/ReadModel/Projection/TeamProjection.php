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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addRank($rank): self
    {
        $this->rank += $rank;

        return $this;
    }

    public function getRank(): int
    {
        return $this->rank;
    }

    public function setStadium($stadium): self
    {
        $this->stadium = $stadium;

        return $this;
    }

    public function getStadium(): string
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
