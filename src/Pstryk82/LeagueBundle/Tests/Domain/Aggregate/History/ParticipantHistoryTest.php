<?php

namespace Pstryk82\LeagueBundle\Tests\Domain\Aggregate\History;

use Pstryk82\LeagueBundle\Domain\Aggregate\History\ParticipantHistory;
use Pstryk82\LeagueBundle\Storage\EventStorage;

class ParticipantHistoryTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $participantId = '21212';
        $eventStorageMock = $this->getMockBuilder(EventStorage::class)->disableOriginalConstructor()->getMock();
        $eventsArray = [
            $this->getMockBuilder(LeagueParticipantWasCreated::class)->disableOriginalConstructor()->getMock(),
        ];

        $eventStorageMock
            ->expects($this->once())
            ->method('find')
            ->with($participantId)
            ->willReturn($eventsArray);
        $history = new ParticipantHistory($participantId, $eventStorageMock);

        $this->assertEquals($eventsArray, $history->getEvents());
        $this->assertEquals($participantId, $history->getAggregateId());
    }
}
