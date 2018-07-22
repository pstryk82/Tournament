<?php

namespace Pstryk82\LeagueBundle\Tests\Domain\Aggregate;

use Pstryk82\LeagueBundle\Domain\Aggregate\AggregateInterface;

abstract class AbstractDomainObjectTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param AggregateInterface $domainObject
     * @param string $eventClass
     */
    protected function assertEventOnDomainObjectWasCreated(AggregateInterface $domainObject, $eventClass)
    {
        $events = $domainObject->getEvents();
        $event = reset($events);
        $this->assertInstanceOf($eventClass, $event);
        $this->assertEquals($domainObject->getAggregateId(), $event->getAggregateId());
    }
}
