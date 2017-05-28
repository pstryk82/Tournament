<?php

namespace Pstryk82\LeagueBundle\Command;

use Pstryk82\LeagueBundle\Domain\Aggregate\AggregateInterface;
use Pstryk82\LeagueBundle\Domain\Aggregate\Game;
use Pstryk82\LeagueBundle\Domain\Aggregate\History\LeagueHistory;
use Pstryk82\LeagueBundle\Domain\Aggregate\History\ParticipantHistory;
use Pstryk82\LeagueBundle\Domain\Aggregate\History\TeamHistory;
use Pstryk82\LeagueBundle\Domain\Aggregate\League;
use Pstryk82\LeagueBundle\Domain\Aggregate\LeagueParticipant;
use Pstryk82\LeagueBundle\Domain\Aggregate\Team;
use Pstryk82\LeagueBundle\Scheduler\LeagueScheduler;
use Pstryk82\LeagueBundle\Storage\EventStorage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * {@codeCoverageIgnore}
 */
class LoadFixturesCommand extends ContainerAwareCommand
{
    /**
     * @var EventStorage
     */
    private $eventStorage;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var string
     */
    private $leagueId;

    /**
     * @var []
     */
    private $teamIds;

    /**
     * @var []
     */
    private $participantIds;

    protected function configure()
    {
        $this
            ->setName('league:fixtures:load')
            ->setDescription('Load defined fixtures for league');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->eventStorage = $this->getContainer()->get('pstryk82.league.event_storage');
        $this->eventDispatcher = $this->getContainer()->get('event_dispatcher');

        $entityManagerEvents = $this->getContainer()->get('doctrine.orm.events_entity_manager');
        $entityManagerEvents->getConnection()->exec('TRUNCATE TABLE stored_event');
        $entityManagerEvents->clear();

        $entityManagerProjections = $this->getContainer()->get('doctrine.orm.projections_entity_manager');
        $entityManagerProjections->getConnection()->exec('DELETE FROM game');
        $entityManagerProjections->getConnection()->exec('DELETE FROM competition');
        $entityManagerProjections->getConnection()->exec('DELETE FROM abstract_participant');
        $entityManagerProjections->getConnection()->exec('DELETE FROM team');
        

        $entityManagerProjections->clear();

        $this->executeLeagueFixtures();
        $this->executeTeamsFixtures();
        $this->executeParticipantsFixtures();
        $this->executeGamesFixtures();
        $this->executeFinishLeague();

        $output->writeln(
            sprintf(
                'Fixtures have been loaded.'
            )
        );
    }

    private function executeLeagueFixtures()
    {
        $league = League::create(
            "Top Clubs' League",
            '2016/2017',
            5,
            2,
            0,
            3,
            1,
            0,
            2
        );
        
        $this->eventStorage->add($league);
        $this->dispatchEvents($league);
        $this->leagueId = $league->getAggregateId();
    }

    private function dispatchEvents(AggregateInterface $aggregate)
    {
        foreach ($aggregate->getEvents() as $event) {
            $this->eventDispatcher->dispatch($event->getEventName(), $event);
        }
    }

    public function executeTeamsFixtures()
    {
        $teamData = json_decode(
            file_get_contents('./src/Pstryk82/LeagueBundle/DataFixtures/teams_8.json')
        );

        foreach ($teamData as $teamRecord) {
            $team = Team::create(
                $teamRecord->name,
                $teamRecord->rank,
                $teamRecord->stadium
            );

            $this->eventStorage->add($team);
            $this->dispatchEvents($team);
            $this->teamIds[] = $team->getAggregateId();
        }
    }

    private function executeParticipantsFixtures()
    {
        $leagueHistory = new LeagueHistory($this->leagueId, $this->eventStorage);
        $league = League::reconstituteFrom($leagueHistory);

        foreach ($this->teamIds as $teamId) {
            $teamHistory = new TeamHistory($teamId, $this->eventStorage);
            $team = Team::reconstituteFrom($teamHistory);

            $participant = $team->registerInLeague($league);
            $this->eventStorage->add($participant);
            $this->dispatchEvents($participant);
            $this->participantIds[] = $participant->getAggregateId();
        }
    }


    private function executeGamesFixtures()
    {
        $leagueHistory = new LeagueHistory($this->leagueId, $this->eventStorage);
        $league = League::reconstituteFrom($leagueHistory);

        $this->participants = [];
        foreach ($this->participantIds as $participantId) {
            $participantHistory = new ParticipantHistory($participantId, $this->eventStorage);
            $participant = LeagueParticipant::reconstituteFrom($participantHistory);
            $this->participants[] = $participant;
        }

        $numberOfParticipants = sizeof($this->participantIds);
        $scheduler = new LeagueScheduler();
        $schedule = $scheduler->generateSchedule($this->participants, $league);

        foreach ($schedule as $round) {
            foreach ($round as $game) {
                $this->generateGameResults($game);
            }
        }


        foreach ($this->participants as $participant) {
            $this->eventStorage->add($participant);
            $this->dispatchEvents($participant);
            $this->eventStorage->add($participant->getTeam());
            $this->dispatchEvents($participant->getTeam());
        }
    }

    /**
     * @param Game $game
     */
    private function generateGameResults(Game $game)
    {
        $game->recordResult(mt_rand(0, 3), mt_rand(0, 3));

        $this->eventStorage->add($game);
        $this->dispatchEvents($game);
    }

    public function executeFinishLeague()
    {
        $leagueHistory = new LeagueHistory($this->leagueId, $this->eventStorage);
        $league = League::reconstituteFrom($leagueHistory);
        $league->finish();
        $this->eventStorage->add($league);
        $this->dispatchEvents($league);
    }
}
