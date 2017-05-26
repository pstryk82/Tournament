<?php

namespace Pstryk82\LeagueBundle\Entity;

use Pstryk82\LeagueBundle\Event\AbstractEvent;

/**
 * StoredEvent
 */
class StoredEvent
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $aggregateId;

    /**
     * @var string
     */
    private $aggregateClass;

    /**
     * @var string
     */
    private $event;

    /**
     * @var \DateTime
     */
    private $happenedAt;


    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set aggregateID
     *
     * @param string $aggregateId
     *
     * @return StoredEvent
     */
    public function setAggregateId($aggregateId)
    {
        $this->aggregateId = $aggregateId;

        return $this;
    }

    /**
     * Get aggregateID
     *
     * @return string
     */
    public function getAggregateId()
    {
        return $this->aggregateId;
    }

    /**
     * Set aggregateClass
     *
     * @param string $aggregateClass
     *
     * @return StoredEvent
     */
    public function setAggregateClass($aggregateClass)
    {
        $this->aggregateClass = $aggregateClass;

        return $this;
    }

    /**
     * Get aggregateClass
     *
     * @return string
     */
    public function getAggregateClass()
    {
        return $this->aggregateClass;
    }

    /**
     * Set event
     *
     * @param AbstractEvent $event
     *
     * @return StoredEvent
     */
    public function setEvent($event)
    {
        $this->event = serialize($event);

        return $this;
    }

    /**
     * Get event
     *
     * @return string
     */
    public function getEvent()
    {
        return unserialize($this->event);
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return StoredEvent
     */
    public function setHappenedAt($createdAt)
    {
        $this->happenedAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getHappenedAt()
    {
        return $this->happenedAt;
    }
}

