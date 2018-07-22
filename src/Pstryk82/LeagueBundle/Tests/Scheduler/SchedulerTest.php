<?php

namespace Pstryk82\LeagueBundle\Tests\Scheduler;

use Pstryk82\LeagueBundle\Domain\Aggregate\Game;
use Pstryk82\LeagueBundle\Domain\Aggregate\League;
use Pstryk82\LeagueBundle\Domain\Aggregate\LeagueParticipant;
use Pstryk82\LeagueBundle\Scheduler\LeagueScheduler;

class SchedulerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var LeagueScheduler
     */
    private $scheduler;

    /**
     * @var League 
     */
    private $league;

    /**
     * @var array
     */
    private $participants;

    public function setUp()
    {
        $this->scheduler = new LeagueScheduler();

        $this->league = new League('league');
        $teamA = new LeagueParticipant('teamA');
        $teamB = new LeagueParticipant('teamB');
        $teamC = new LeagueParticipant('teamC');
        $teamD = new LeagueParticipant('teamD');
        $teamE = new LeagueParticipant('teamE');
        $teamF = new LeagueParticipant('teamF');
        $teamG = new LeagueParticipant('teamG');
        $teamH = new LeagueParticipant('teamH');
        $this->participants = [
            $teamA, $teamB, $teamC, $teamD, $teamE, $teamF, $teamG, $teamH
        ];
    }

    public function tearDown()
    {
        unset($this->scheduler, $this->participants, $this->league);
    }

    public function testEvenNumberOfParticipantsSingleLeg()
    {
        $this->league->setNumberOfLegs(1);
        $this->checkEvenNumberOfParticipants();
    }

    public function testEvenNumberOfParticipantsTwoLegs()
    {
        $this->league->setNumberOfLegs(2);
        $this->checkEvenNumberOfParticipants();
    }

    public function testOddNumberOfParticipantsSingleLeg()
    {
        $this->league->setNumberOfLegs(1);
        array_pop($this->participants);
        $this->checkOddNumberOfParticipants();
    }

    public function testOddNumberOfParticipantsTwoLegs()
    {
        $this->league->setNumberOfLegs(2);
        array_pop($this->participants);
        $this->checkOddNumberOfParticipants();
    }

    public function testManyParticipantsManyLegs()
    {
        $teamI = new LeagueParticipant('teamI');
        $teamJ = new LeagueParticipant('teamJ');
        $teamK = new LeagueParticipant('teamK');
        $teamL = new LeagueParticipant('teamL');
        $teamM = new LeagueParticipant('teamM');
        $teamN = new LeagueParticipant('teamN');
        $teamO = new LeagueParticipant('teamO');
        $teamP = new LeagueParticipant('teamP');
        $this->participants = array_merge(
            $this->participants,
            [$teamI, $teamJ, $teamK, $teamL, $teamM, $teamN, $teamO, $teamP]
        );
        $this->league->setNumberOfLegs(6);
        $this->checkEvenNumberOfParticipants();
    }

    private function checkEvenNumberOfParticipants()
    {
        $schedule = $this->scheduler->generateSchedule($this->participants, $this->league);

        $this->assertCount((sizeof($this->participants)-1) * $this->league->getNumberOfLegs(), $schedule);

        $this->checkGeneric($schedule);
    }

    private function checkGeneric(array $schedule)
    {
        foreach ($this->participants as $participant) {
            $this->assertTeamGamesCount($schedule, $participant);
        }

        $simpleScheduleView = $this->getSimpleScheduleView($schedule);

        $simpleScheduleStructure = $this->getSimpleScheduleStructure($schedule);
        foreach ($this->participants as $participant) {
            $this->assertAlmostEqualNumberOfHomeAndAwayGames($participant->getAggregateId(), $simpleScheduleStructure);
            $this->assertPlayEachOpponentThatManyTimes(
                $participant->getAggregateId(),
                $this->league->getNumberOfLegs(),
                $simpleScheduleStructure
            );
        }
    }

    private function checkOddNumberOfParticipants()
    {
        $schedule = $this->scheduler->generateSchedule($this->participants, $this->league);

        $this->assertCount(sizeof($this->participants) * $this->league->getNumberOfLegs(), $schedule);

        $this->checkGeneric($schedule);
    }

    private function assertTeamGamesCount(array $schedule, LeagueParticipant $participant)
    {
        $teamGamesCount = 0;
        foreach ($schedule as $round) {
            $plannedInThisRound = [];
            foreach ($round as $game) {
                /* @var $game Game */
                $home = $game->getHomeParticipant()->getAggregateId();
                $away = $game->getAwayParticipant()->getAggregateId();
                $this->assertNotSame($home, $away);
                $this->assertFalse(
                    isset($plannedInThisRound[$home]),
                    sprintf('Team %s is planned to play more than once in round %d', $home, $round)
                );
                $this->assertFalse(
                    isset($plannedInThisRound[$away]),
                    sprintf('Team %s is planned to play more than once in round %d', $away, $round)
                );
                $plannedInThisRound[$home] = true;
                $plannedInThisRound[$away] = true;
                if ($game->getHomeParticipant()->getAggregateId() == $participant->getAggregateId()
                        || $game->getAwayParticipant()->getAggregateId() == $participant->getAggregateId()) {
                    $teamGamesCount++;
                }
            }
        }
        $this->assertEquals((sizeof($this->participants) - 1) * $this->league->getNumberOfLegs(), $teamGamesCount);
    }

    protected function assertAlmostEqualNumberOfHomeAndAwayGames($aggregateId, $simpleScheduleStructure)
    {
        $homeGames = 0;
        $awayGames = 0;
        foreach ($simpleScheduleStructure as $game) {
            if ($game['home'] == $aggregateId) {
                $homeGames++;
            } elseif ($game['away'] == $aggregateId) {
                $awayGames++;
            }
        }
        $this->assertLessThanOrEqual(
            1,
            abs($homeGames - $awayGames),
            sprintf('Team %s is planned to play %d home games and %d away games', $aggregateId, $homeGames, $awayGames)
        );
    }

    protected function assertPlayEachOpponentThatManyTimes($aggregateId, $howManyTimes, $simpleScheduleStructure)
    {
        $opponents = [];
        foreach ($simpleScheduleStructure as $game) {
            if ($game['home'] == $aggregateId) {
                $opponentId = $game['away'];
                isset($opponents[$opponentId]) ? $opponents[$opponentId]++ : $opponents[$opponentId] = 1;
            } elseif ($game['away'] == $aggregateId) {
                $opponentId = $game['home'];
                isset($opponents[$opponentId]) ? $opponents[$opponentId]++ : $opponents[$opponentId] = 1;
            }
            
        }

        foreach ($opponents as $opponent => $howManyTimesAgainstThisOpponent) {
            $this->assertEquals(
                $howManyTimes,
                $howManyTimesAgainstThisOpponent,
                sprintf(
                    'Team %s played against Team %s %d times, %d expected',
                    $aggregateId,
                    $opponent,
                    $howManyTimesAgainstThisOpponent,
                    $howManyTimes
                )
            );
        }
    }

    protected function getSimpleScheduleStructure($schedule)
    {
        $output = [];
        foreach ($schedule as $roundNumber => $games) {
            foreach ($games as $game) {
                $match = [
                    'round' => $roundNumber,
                    'home' => $game->getHomeParticipant()->getAggregateId(),
                    'away' => $game->getAwayParticipant()->getAggregateId(),
                ];

                $output[] = $match;
            }
        }

        return $output;
    }

    protected function getSimpleScheduleView($schedule)
    {
        $output = [];
        foreach ($schedule as $roundNumber => $games) {
            foreach ($games as $game) {
                $description = sprintf(
                    "%d: %s vs %s", $roundNumber, $game->getHomeParticipant()->getAggregateId(), $game->getAwayParticipant()->getAggregateId()
                );
                $output[] = $description;
            }
        }

        return $output;
    }

}
