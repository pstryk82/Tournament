<?php

namespace Pstryk82\LeagueBundle\Scheduler;

use Pstryk82\LeagueBundle\Domain\Aggregate\Game;
use Pstryk82\LeagueBundle\Domain\Aggregate\League;

class LeagueScheduler
{
    /**
     * @param array $participants
     * @param League $league
     *
     * @return array
     */
    public function generateSchedule(array $participants, League $league)
    {
        $schedule = [];
        $numberOfParticipants = sizeof($participants);
        $numberOfRoundsInSeries = $numberOfParticipants % 2 == 0 ? $numberOfParticipants - 1 : $numberOfParticipants;
        $round = 0;

        for ($j = 1; $j <= $league->getNumberOfLegs(); $j++) {
            $firstOnePlaysHome = $j % 2 == 0;
            for ($i = 1; $i <= $numberOfRoundsInSeries; $i++) {
                $round++;
                $schedule[$round] = $this->matchPairs($participants, $league, $round, $firstOnePlaysHome);
                $this->shiftParticipants($participants);
            }
        }

        return $schedule;
    }

    /**
     * @param array $participants
     * @param League $league
     * @param int $round
     * @param bool $firstOnePlaysHome
     * 
     * @return []
     */
    private function matchPairs(array $participants, League $league, $round, $firstOnePlaysHome)
    {
        try{
            $games[] = $this->matchFirstParticipant($participants, $round, $league);
        } catch (OddNumberOfParticipantsException $e) {
            $pausedInCurrentRound = array_shift($participants);
            $games = [];
        }
        
        $games = array_merge($games, $this->matchOtherParticipants($participants, $firstOnePlaysHome, $league, $round));

        return $games;
    }

    /**
     * @param array $participants
     * @param int $round
     * @param League $league
     *
     * @return Game
     * 
     * @throws OddNumberOfParticipantsException
     */
    private function matchFirstParticipant(array &$participants, $round, League $league)
    {
        if (sizeof($participants) % 2 == 0) {
            $zeroth = array_shift($participants);
            if ($round % 2 == 1) {
                $game = Game::create($zeroth, array_shift($participants), $league, new \DateTime(), $round);
            } else {
                $game = Game::create(array_shift($participants), $zeroth, $league, new \DateTime(), $round);
            }

            return $game;
        }

        throw new OddNumberOfParticipantsException();
    }

    /**
     *
     * @param array $participants
     * @param bool$firstOnePlaysHome
     * @param League $league
     * 
     * @return Game[]
     */
    private function matchOtherParticipants(array &$participants, &$firstOnePlaysHome, League $league, $round)
    {
        while (!empty($participants)) {
            if ($firstOnePlaysHome) {
                $home = array_shift($participants);
                $away = array_pop($participants);
            } else {
                $away = array_shift($participants);
                $home = array_pop($participants);
            }
            $game = Game::create($home, $away, $league, new \DateTime(), $round);
            $games[] = $game;
            $firstOnePlaysHome = !$firstOnePlaysHome;
        }

        return $games;
    }

    /**
     * @param array $participants
     */
    private function shiftParticipants(array &$participants)
    {
        if (sizeof($participants) % 2 == 0) {
            $zeroth = array_shift($participants);
            $this->moveFirstElementToEnd($participants);
            array_unshift($participants, $zeroth);
        } else {
            $this->moveFirstElementToEnd($participants);
        }
    }

    /**
     * @param array $participants
     */
    private function moveFirstElementToEnd(array &$participants)
    {
        $theOneToMove = array_shift($participants);
        array_push($participants, $theOneToMove);
    }

}
