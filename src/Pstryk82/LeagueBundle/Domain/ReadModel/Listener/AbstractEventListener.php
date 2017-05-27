<?php

namespace Pstryk82\LeagueBundle\Domain\ReadModel\Listener;

use Pstryk82\LeagueBundle\Event\AbstractEvent;
use Pstryk82\LeagueBundle\Storage\ProjectionStorage;

abstract class AbstractEventListener
{
    /**
     * @var ProjectionStorage
     */
    protected $projectionStorage;

    /**
     * @param ProjectionStorage $projectionStorage
     */
    public function __construct(ProjectionStorage $projectionStorage)
    {
        $this->projectionStorage = $projectionStorage;
    }
    /**
     * @param DomainEvent $event
     */
    public function when(AbstractEvent $event)
    {
        $method = explode('\\', get_class($event));
        $method = 'on' . end($method);
        if (method_exists($this, $method)) {
            $this->$method($event);
        }
    }
}
