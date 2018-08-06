<?php

namespace Pstryk82\LeagueBundle\Tests\Api;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pstryk82\LeagueBundle\Api\League\Round;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\LeagueProjection;
use Pstryk82\LeagueBundle\Exception\LeagueNotFoundException;
use Pstryk82\LeagueBundle\Exception\RoundNotFoundException;

class RoundTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var EntityManagerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var Round
     */
    private $roundApi;

    public function setUp()
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->roundApi = new Round($this->entityManagerMock);
    }

    public function tearDown()
    {
        unset($this->roundApi, $this->entityManagerMock);
    }

    public function testShowTournamentDoesNotExist()
    {
        $this->entityManagerMock
            ->expects($this->at(0))
            ->method('getRepository')
            ->willReturn($this->createMock(ObjectRepository::class));
        $this->expectException(LeagueNotFoundException::class);

        $this->roundApi->show('abcd123', 1);
    }

    public function testShowTournamentRoundDoesNotExist()
    {
        $objectRepositoryLeague = $this->createMock(ObjectRepository::class);
        $this->entityManagerMock
            ->expects($this->at(0))
            ->method('getRepository')
            ->willReturn($objectRepositoryLeague);
        $leagueProjection = new LeagueProjection('abcd123');
        $objectRepositoryLeague->method('find')->willReturn($leagueProjection);

        $objectRepositoryGame = $this->createMock(ObjectRepository::class);
        $this->entityManagerMock
            ->expects($this->at(1))
            ->method('getRepository')
            ->willReturn($objectRepositoryGame);
        $objectRepositoryLeague->method('findBy')->willReturn([]);
        $this->expectException(RoundNotFoundException::class);

        $this->roundApi->show('abcd123', 19);
    }

}
