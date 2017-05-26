<?php

namespace Pstryk82\LeagueBundle\EventEngine;

use Pstryk82\LeagueBundle\Domain\ReadModel\Listener\AbstractEventListener;
use Pstryk82\LeagueBundle\Event\AbstractEvent;

class EventBus
{
    /**
     * @var $listeners AbstractEventListener[]
     */
    private $listeners = [];

    public function registerListener(AbstractEventListener $domainEventListener)
    {
        $this->listeners[] = $domainEventListener;
    }
    /**
     * @param $events AbstractEvent[]
     */
    public function dispatch(array $events)
    {
        /** @var AbstractEvent $event */
        foreach ($events as &$event) {
            /** @var AbstractEventListener $listener */
            foreach ($this->listeners as &$listener) {
                $listener->when($event);
            }
        }
    }
}
