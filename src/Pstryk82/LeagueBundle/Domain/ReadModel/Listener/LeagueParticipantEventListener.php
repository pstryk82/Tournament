<?php

namespace Pstryk82\LeagueBundle\Domain\ReadModel\Listener;

use Pstryk82\LeagueBundle\Domain\Aggregate\LeagueParticipant;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\LeagueParticipantProjection;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\LeagueProjection;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\TeamProjection;
use Pstryk82\LeagueBundle\Event\LeagueParticipantWasCreated;
use Pstryk82\LeagueBundle\Event\ParticipantHasDrawn;
use Pstryk82\LeagueBundle\Event\ParticipantHasLost;
use Pstryk82\LeagueBundle\Event\ParticipantHasWon;

class LeagueParticipantEventListener extends AbstractEventListener
{
    /**
     * @param LeagueParticipantWasCreated $event
     */
    public function onLeagueParticipantWasCreated(LeagueParticipantWasCreated $event)
    {
        $leagueParticipant = new LeagueParticipantProjection(
            $event->getAggregateId()
        );
        $team = $this->projectionStorage->find($event->getTeam()->getAggregateId(), TeamProjection::class);
        $leagueParticipant->setTeam($team);

        $competition = $this->projectionStorage->find($event->getLeague()->getAggregateId(), LeagueProjection::class);
        $leagueParticipant->setCompetition($competition);

        $this->projectionStorage->save($leagueParticipant);
    }

    /**
     * @param ParticipantHasWon $event
     */
    public function onParticipantHasWon(ParticipantHasWon $event)
    {
        /* @var  $leagueParticipant LeagueParticipant */
        $leagueParticipant = $this->projectionStorage->find(
            $event->getGameOutcomeResolver()->getWinner()->getAggregateId(), LeagueParticipantProjection::class
        );
        /* @var $league LeagueProjection */
        $league = $this->projectionStorage->find(
            $event->getCompetition()->getAggregateId(), LeagueProjection::class
        );
        $leagueParticipant
            ->addPoints($league->getPointsForWin())
            ->addGoalsFor($event->getGameOutcomeResolver()->getWinnerScore())
            ->addGoalsAgainst($event->getGameOutcomeResolver()->getLoserScore())
            ->addGoalDifference(
                $event->getGameOutcomeResolver()->getWinnerScore() - $event->getGameOutcomeResolver()->getLoserScore()
            )
            ->addGamesPlayed(1);

        $this->projectionStorage->save($leagueParticipant);
    }

    /**
     * @param ParticipantHasLost $event
     */
    public function onParticipantHasLost(ParticipantHasLost $event)
    {
        /* @var  $leagueParticipant LeagueParticipant */
        $leagueParticipant = $this->projectionStorage->find(
            $event->getGameOutcomeResolver()->getLoser()->getAggregateId(), LeagueParticipantProjection::class
        );
        /* @var $league LeagueProjection */
        $league = $this->projectionStorage->find(
            $event->getCompetition()->getAggregateId(), LeagueProjection::class
        );
        $leagueParticipant
            ->addPoints($league->getPointsForLose())
            ->addGoalsFor($event->getGameOutcomeResolver()->getLoserScore())
            ->addGoalsAgainst($event->getGameOutcomeResolver()->getWinnerScore())
            ->addGoalDifference(
                $event->getGameOutcomeResolver()->getLoserScore() - $event->getGameOutcomeResolver()->getWinnerScore()
            )
            ->addGamesPlayed(1);

        $this->projectionStorage->save($leagueParticipant);
    }

    /**
     * @param ParticipantHasWon $event
     */
    public function onParticipantHasDrawn(ParticipantHasDrawn $event)
    {
        /* @var  $leagueParticipant LeagueParticipant */
        $leagueParticipant = $this->projectionStorage->find(
            $event->getAggregateId(), LeagueParticipantProjection::class
        );
        /* @var $league LeagueProjection */
        $league = $this->projectionStorage->find(
            $event->getCompetition()->getAggregateId(), LeagueProjection::class
        );
        $leagueParticipant
            ->addPoints($league->getPointsForDraw())
            ->addGoalsFor($event->getDrawScore())
            ->addGoalsAgainst($event->getDrawScore())
            ->addGamesPlayed(1);

        $this->projectionStorage->save($leagueParticipant);
    }
}
