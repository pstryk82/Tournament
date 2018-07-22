<?php

namespace Pstryk82\LeagueBundle\Api\League;

use Doctrine\ORM\EntityManagerInterface;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\GameProjection;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\LeagueProjection;
use Pstryk82\LeagueBundle\Exception\LeagueNotFoundException;
use Pstryk82\LeagueBundle\Exception\RoundNotFoundException;

class Round
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws LeagueNotFoundException
     * @throws RoundNotFoundException
     */
    public function show($leagueId, $round): array
    {
        $leagueProjection = $this->entityManager->getRepository(LeagueProjection::class)->find($leagueId);
        if (is_null($leagueProjection)) {
            throw new LeagueNotFoundException(
                sprintf('Tournament with id %s does not exist.', $leagueId)
            );
        }

        $gamesProjections = $this->entityManager->getRepository(GameProjection::class)->findBy(
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

        return $content;
    }
}
