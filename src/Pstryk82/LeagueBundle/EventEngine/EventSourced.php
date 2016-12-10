<?php

namespace Pstryk82\LeagueBundle\EventEngine;

use Exception;
use Pstryk82\LeagueBundle\Domain\Aggregate\History\AggregateHistoryInterface;
use Pstryk82\LeagueBundle\Event\AbstractEvent;

trait EventSourced
{
    /**
     * @var $events AbstractEvent[]
     */
    protected $events = [];
    /**
     * @return AbstractEvent[]
     */
    public function getEvents()
    {
        return $this->events;
    }
    /**
     * @param AbstractEvent $event
     */
    protected function recordThat(AbstractEvent $event)
    {
        $this->events[] = $event;
    }
    /**
     * @param AbstractEvent $event
     */
    private function apply(AbstractEvent $event)
    {
        $method = explode('\\', get_class($event));
        $method = 'apply' . end($method);
        $this->$method($event);
    }

    /**
     * @return string
     */
    abstract public function getAggregateId();

    /**
     * @param LegueHistory $aggregateHistory
     * @return \self
     */
    public static function reconstituteFrom(AggregateHistoryInterface $aggregateHistory)
    {
        $instance = new static($aggregateHistory->getAggregateId());
        foreach ($aggregateHistory->getEvents() as $event) {
            $applyMethod = 'apply' . (new \ReflectionClass($event))->getShortName();
//            $applyMethod = explode('\\', get_class($event));
//            $applyMethod = 'apply'.end($applyMethod);
            $instance->$applyMethod($event);
        }

        return $instance;
    }

    public static function create()
    {
        throw new Exception('Method create was not implemented in class: '.self::class);
    }
}
