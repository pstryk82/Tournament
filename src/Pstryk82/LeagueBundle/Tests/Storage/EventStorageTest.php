<?php

namespace Pstryk82\LeagueBundle\Tests\Storage;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use Pstryk82\LeagueBundle\Domain\Aggregate\League;
use Pstryk82\LeagueBundle\Entity\StoredEvent;
use Pstryk82\LeagueBundle\Event\LeagueWasFinished;
use Pstryk82\LeagueBundle\Generator\IdGenerator;
use Pstryk82\LeagueBundle\Repository\StoredEventRepository;
use Pstryk82\LeagueBundle\Storage\EventStorage;

class EventStorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var EventStorage
     */
    private $storage;

    /**
     *
     * @var StoredEventRepository
     */
    private $storedEventRepositoryMock;

    /**
     * @var EntityManager
     */
    private $entityManagerMock;

    public function setUp()
    {
        $this->storedEventRepositoryMock = $this->getMockBuilder(StoredEventRepository::class)
            ->disableOriginalConstructor()->getMock();
        $this->entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()->getMock();
        $this->storage = new EventStorage($this->storedEventRepositoryMock, $this->entityManagerMock);
    }

    public function tearDown()
    {
        unset($this->storedEventRepositoryMock, $this->entityManagerMock, $this->storage);
    }

    public function testFind()
    {
        $aggregateId = '17f';
        $now = new \DateTime();
        $storedEvent = new StoredEvent();
        $storedEvent
            ->setAggregateId($aggregateId)
            ->setAggregateClass('LeagueWasFinished')
            ->setEvent(new LeagueWasFinished($aggregateId, $now))
            ->setHappenedAt($now);

        $this->storedEventRepositoryMock->method('findBy')
            ->with(['aggregateId' => '17f'])
            ->willReturn([$storedEvent]);

        $result = $this->storage->find($aggregateId);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(LeagueWasFinished::class, reset($result));
    }

    public function testAdd()
    {
        $aggregateId = 'bcf';
        $now = new \DateTime();

        $event = new LeagueWasFinished($aggregateId, $now);

        $storedEvent = new StoredEvent();
        $storedEvent
            ->setAggregateId($event->getLeagueId())
            ->setAggregateClass(League::class)
            ->setEvent($event)
            ->setHappenedAt($event->getHappenedAt());

        $this->entityManagerMock
            ->expects($this->at(0))
            ->method('persist')
            ->with($storedEvent);
        $this->entityManagerMock
            ->expects($this->at(1))
            ->method('flush');

        $this->storage->add($event, League::class);
    }
}
