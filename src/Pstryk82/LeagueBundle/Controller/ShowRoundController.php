<?php

namespace Pstryk82\LeagueBundle\Controller;

use Doctrine\ORM\EntityManager;
use Pstryk82\LeagueBundle\Api\League\Round;
use Symfony\Component\HttpFoundation\JsonResponse;

class ShowRoundController
{
    /**
     * @var Round
     */
    private $round;

    public function __construct(Round $round)
    {
        $this->round = $round;
    }

     public function showRoundAction($leagueId, $round): JsonResponse
    {
        $content = $this->round->show($leagueId, $round);

        $response = new JsonResponse($content);

        return $response;
    }
}
