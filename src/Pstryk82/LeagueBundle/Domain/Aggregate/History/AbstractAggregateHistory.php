<?php

namespace Pstryk82\LeagueBundle\Domain\Aggregate\History;

use Pstryk82\LeagueBundle\Event\AbstractEvent;
use Pstryk82\LeagueBundle\Storage\EventStorage;

abstract class AbstractAggregateHistory implements AggregateHistoryInterface
{
    /**
     * @var string
     */
    protected $aggregateId;

    /**
     * @var AbstractEvent[]
     */
    protected $events;

    public function __construct(string $aggregateId, EventStorage $eventStorage)
    {
        $this->aggregateId = $aggregateId;
        $this->events = $eventStorage->find($aggregateId);
    }

    public function getAggregateId()
    {
        return $this->aggregateId;
    }

    public function getEvents()
    {
        return $this->events;
    }

}
