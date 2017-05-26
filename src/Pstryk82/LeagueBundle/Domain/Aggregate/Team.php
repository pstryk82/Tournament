<?php

namespace Pstryk82\LeagueBundle\Domain\Aggregate;

use Pstryk82\LeagueBundle\Event\TeamGainedRankPoints;
use Pstryk82\LeagueBundle\Event\TeamWasCreated;
use Pstryk82\LeagueBundle\EventEngine\EventSourced;
use Pstryk82\LeagueBundle\Generator\IdGenerator;

class Team implements AggregateInterface
{
    use EventSourced;

    /**
     * @var int
     */
    private $aggregateId;

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

    public function __construct($aggregateId)
    {
        $this->aggregateId = $aggregateId;
    }

    public function getAggregateId()
    {
        return $this->aggregateId;
    }

    /**
     *
     * @param string $name
     * @param int $rank
     * @param string $stadium
     * 
     * @return $this
     */
    public static function create($name, $rank, $stadium)
    {
        $team = new self($aggregateId = IdGenerator::generate());
        $team
            ->setName($name)
            ->addRank($rank)
            ->setStadium($stadium);

        $teamWasCreatedEvent = new TeamWasCreated(
            $aggregateId, $name, $rank, $stadium, new \DateTime()
        );

        $team->recordThat($teamWasCreatedEvent);

        return $team;
    }

    private function applyTeamWasCreated(TeamWasCreated $event)
    {
        $this
            ->setName($event->getName())
            ->addRank($event->getRank())
            ->setStadium($event->getStadium());
    }

    /**
     * @param Competition $league
     *
     * @return AbstractParticipant
     */
    public function registerInLeague($league)
    {
        $participant = LeagueParticipant::create($this, $league);

        return $participant;
    }

    /**
     * @param int $numberOfAddedRankPoints
     */
    public function recordRankPoints($numberOfAddedRankPoints)
    {
        $teamGainedRankPoints = new TeamGainedRankPoints(
            $this->aggregateId,
            $numberOfAddedRankPoints,
            new \DateTime()
        );
        $this->recordThat($teamGainedRankPoints);
        $this->apply($teamGainedRankPoints);
    }

    /**
     * @param TeamGainedRankPoints $event
     */
    private function applyTeamGainedRankPoints(TeamGainedRankPoints $event)
    {
        $this->addRank($event->getNumberOfAddedRankPoints());
    }

    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }

    public function addRank($rank)
    {
        $this->rank += $rank;

        return $this;
    }

    public function setStadium($stadium)
    {
        $this->stadium = $stadium;

        return $this;
    }
}
