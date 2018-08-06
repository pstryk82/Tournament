<?php

namespace Pstryk82\LeagueBundle\Tests\Api;

use Pstryk82\LeagueBundle\Api\League\Round;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\LeagueProjection;

class RoundFunctionalTest extends AbstractDbFunctionalTest
{
    /**
     * @var Round
     */
    private $roundApi;

    public function setUp()
    {
        parent::setUp();
        $this->roundApi = $this->container->get('test.pstryk82_league.api_league.round');
    }

    public function tearDown()
    {
        unset($this->roundApi);
        parent::tearDown();
    }

    public function testShowTournamentRound()
    {
        $leagueProjection = $this->entityManager->getRepository(LeagueProjection::class)->find('League');
        $this->assertNotEmpty('remove me');
    }

}
