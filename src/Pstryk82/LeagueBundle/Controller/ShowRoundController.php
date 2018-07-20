<?php

namespace Pstryk82\LeagueBundle\Controller;

use Doctrine\ORM\EntityManager;
use Pstryk82\LeagueBundle\Exception\LeagueNotFoundException;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\GameProjection;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\LeagueProjection;
use Pstryk82\LeagueBundle\Exception\RoundNotFoundException;
use Pstryk82\LeagueBundle\Storage\EventStorage;
use Symfony\Component\HttpFoundation\JsonResponse;

class ShowRoundController
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @throws LeagueNotFoundException
     * @throws RoundNotFoundException
     */
    public function showRoundAction($leagueId, $round): JsonResponse
    {
        $leagueProjection = $this->em->getRepository(LeagueProjection::class)->find($leagueId);
        if (is_null($leagueProjection)) {
            throw new LeagueNotFoundException(
                sprintf('Tournament with id %s does not exist.', $leagueId)
            );
        }

        $gamesProjections = $this->em->getRepository(GameProjection::class)->findBy(
            [
                'competition' => $leagueProjection,
                'round' => $round
            ]
        );

        if (empty($gamesProjections)) {
            throw new RoundNotFoundException(
                sprintf("Tournament with id %s does not have round '%s'.", $leagueId, $round)
            );
        }

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
