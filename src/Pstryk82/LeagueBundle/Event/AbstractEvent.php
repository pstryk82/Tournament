<?php

namespace Pstryk82\LeagueBundle\Event;

use Symfony\Component\EventDispatcher\Event;

abstract class AbstractEvent extends Event
{
    protected $eventName;

    /**
     * @var string
     */
    protected $aggregateId;

    /**
     * @var \DateTime
     */
    protected $happenedAt;

    /**
     * @return string
     */
    public function getAggregateId()
    {
        return $this->aggregateId;
    }

    /**
     * @return \DateTime
     */
    public function getHappenedAt()
    {
        return $this->happenedAt;
    }


    public function getEventName()
    {
        return $this->eventName;
    }
}
