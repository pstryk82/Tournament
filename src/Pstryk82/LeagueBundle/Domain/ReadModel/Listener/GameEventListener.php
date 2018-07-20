<?php

namespace Pstryk82\LeagueBundle\Domain\ReadModel\Listener;

use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\AbstractParticipantProjection;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\CompetitionProjection;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\GameProjection;
use Pstryk82\LeagueBundle\Event\GameWasPlanned;
use Pstryk82\LeagueBundle\Event\GameWasPlayed;

class GameEventListener extends AbstractEventListener
{
    public function onGameWasPlanned(GameWasPlanned $event)
    {
        $homeParticipantProjection = $this->projectionStorage->find(
            $event->getHomeParticipant()->getAggregateId(),
            AbstractParticipantProjection::class
        );
        $awayParticipantProjection = $this->projectionStorage->find(
            $event->getAwayParticipant()->getAggregateId(),
            AbstractParticipantProjection::class
        );
        $competitionProjection = $this->projectionStorage->find(
            $event->getCompetition()->getAggregateId(),
            CompetitionProjection::class
        );

        $gameProjection = new GameProjection($event->getAggregateId());
        $gameProjection
            ->setHomeParticipant($homeParticipantProjection)
            ->setAwayParticipant($awayParticipantProjection)
            ->setCompetition($competitionProjection)
            ->setBeginningTime($event->getBeginningTime())
            ->setRound($event->getRound())
            ->setOnNeutralGround($event->getOnNeutralGround())
            ->setPlayed(false);

        $this->projectionStorage->save($gameProjection);
    }

    public function onGameWasPlayed(GameWasPlayed $event)
    {
        /* @var $gameProjection GameProjection */
        $gameProjection = $this->projectionStorage->find($event->getAggregateId(), GameProjection::class);
        $gameProjection
            ->setHomeScore($event->getHomeScore())
            ->setAwayScore($event->getAwayScore())
            ->setPlayed($event->getPlayed());

        $this->projectionStorage->save($gameProjection);
    }
}
