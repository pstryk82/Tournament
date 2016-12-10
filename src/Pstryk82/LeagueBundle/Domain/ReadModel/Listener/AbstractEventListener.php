<?php

namespace Pstryk82\LeagueBundle\Domain\ReadModel\Listener;

use Pstryk82\LeagueBundle\Event\AbstractEvent;
use Pstryk82\LeagueBundle\EventEngine\EventBus;
use Pstryk82\LeagueBundle\Storage\ProjectionStorage;

abstract class AbstractEventListener
{
    /**
     * @var ProjectionStorage
     */
    protected $projectionStorage;

    /**
     * @param EventBus $eventBus
     * @param ProjectionStorage $projectionStorage
     */
    public function __construct(EventBus $eventBus, ProjectionStorage $projectionStorage)
    {
        $eventBus->registerListener($this);
        $this->projectionStorage = $projectionStorage;
    }
    /**
     * @param DomainEvent $event
     */
    public function when(AbstractEvent $event)
    {
        $method = 'on' . (new \ReflectionClass($event))->getShortName();
//        $method = explode('\\', get_class($event));
//        $method = 'on' . end($method);
        if (method_exists($this, $method)) {
            $this->$method($event);
        }
    }
}
