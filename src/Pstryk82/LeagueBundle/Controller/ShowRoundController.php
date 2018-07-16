<?php

namespace Pstryk82\LeagueBundle\Controller;

use Doctrine\ORM\EntityManager;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\GameProjection;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\LeagueProjection;
use Pstryk82\LeagueBundle\Storage\EventStorage;
use Symfony\Component\HttpFoundation\JsonResponse;

class ShowRoundController
{

    /**
     * @var EventStorage
     */
    private $eventStorage;

    public function __construct(EventStorage $eventStorage, EntityManager $em)
    {
        $this->eventStorage = $eventStorage;
        $this->em = $em;
    }

    public function showRoundAction($leagueId, $round)
    {
        $leagueProjection = $this
            ->em->getRepository(LeagueProjection::class)->find($leagueId);
        $gamesProjections = $this->em->getRepository(GameProjection::class)->findBy(
            [
                'competition' => $leagueProjection,
                'round' => $round
            ]
        );

        $content = [
            'round' => $round,
        ];
        foreach ($gamesProjections as $game) {
            $content['games'][] = [
                'homeParticipantTeam' => (string)$game->getHomeParticipant()->getTeam(),
                'awayParticipantTeam' => (string)$game->getAwayParticipant()->getTeam(),
                'homeScore' => $game->getHomeScore(),
                'awayScore' => $game->getAwayScore()
            ];
        }

        $response = new JsonResponse($content);

        return $response;

    }
}
