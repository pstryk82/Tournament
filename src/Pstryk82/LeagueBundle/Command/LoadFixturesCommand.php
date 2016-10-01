<?php

namespace Pstryk82\LeagueBundle\Command;

use Pstryk82\LeagueBundle\Domain\Aggregate\History\LeagueHistory;
use Pstryk82\LeagueBundle\Domain\Aggregate\History\TeamHistory;
use Pstryk82\LeagueBundle\Domain\Aggregate\League;
use Pstryk82\LeagueBundle\Domain\Aggregate\Team;
use Pstryk82\LeagueBundle\EventEngine\EventBus;
use Pstryk82\LeagueBundle\Storage\EventStorage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadFixturesCommand extends ContainerAwareCommand
{
    /**
     * @var EventStorage
     */
    private $eventStorage;

    /**
     * @var EventBus
     */
    private $eventBus;

    /**
     * @var string
     */
    private $leagueId;

    /**
     * @var Team[]
     */
    private $teamIds;

    protected function configure()
    {
        $this
            ->setName('league:fixtures:load')
            ->setDescription('Load defined fixtures for league');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // need to initialize listeners explicitly so they register themselves in the EventBus
        $this->leagueProjectionListener = $this->getContainer()->get('pstryk82.league.listener.league');
        $this->teamEventListener = $this->getContainer()->get('pstryk82.league.listener.team');
        $this->leagueParticipantEventListener = $this->getContainer()->get('pstryk82.league.listener.league_participant');

        
        $this->eventStorage = $this->getContainer()->get('pstryk82.league.event_storage');
        $this->eventBus = $this->getContainer()->get('pstryk82.league.event_bus');

        $entityManagerEvents = $this->getContainer()->get('doctrine.orm.events_entity_manager');
        $entityManagerEvents->getConnection()->exec('TRUNCATE TABLE stored_event');
        $entityManagerEvents->clear();

        $entityManagerProjections = $this->getContainer()->get('doctrine.orm.projections_entity_manager');
        $entityManagerProjections->getConnection()->exec('DELETE FROM competition');
        $entityManagerProjections->getConnection()->exec('DELETE FROM abstract_participant');
        $entityManagerProjections->getConnection()->exec('DELETE FROM team');

        $entityManagerProjections->clear();

        $this->executeLeagueFixtures();
        $this->executeTeamsFixtures();
        $this->executeParticipantsFixtures();

        $this->showParticipants();

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
        
        $this->eventBus->dispatch($league->getEvents());
        $this->eventStorage->add($league);

        $this->leagueId = $league->getAggregateId();
    }


    public function executeTeamsFixtures()
    {
        $teamData = [
            [
                'name' => 'Real Madrid CF',
                'rank' => 144428,
                'stadium' => 'Santiago Bernabeu',
            ],
            [
                'name' => 'FC Bayern Muenchen',
                'rank' => 134528,
                'stadium' => 'Allianz Arena',
            ],
            [
                'name' => 'FC Barcelona',
                'rank' => 129428,
                'stadium' => 'Camp Nou',
            ],
            [
                'name' => 'Club Atletico de Madrid',
                'rank' => 114428,
                'stadium' => 'Vicente Calderon',
            ],
            [
                'name' => 'Juventus',
                'rank' => 109199,
                'stadium' => 'Juventus Stadium',
            ],
            [
                'name' => 'Paris Saint-Germain',
                'rank' => 108066,
                'stadium' => 'Parc des Princes',
            ],
            [
                'name' => 'Borussia Dortmund',
                'rank' => 104528,
                'stadium' => 'Signal Iduna Park',
            ],
            [
                'name' => 'Chelsea FC',
                'rank' => 103763,
                'stadium' => 'Stamford Bridge',
            ],
        ];

        foreach ($teamData as $teamRecord) {
            $team = Team::create(
                $teamRecord['name'],
                $teamRecord['rank'],
                $teamRecord['stadium']
            );

            $this->eventBus->dispatch($team->getEvents());
            $this->eventStorage->add($team);
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
            $this->eventBus->dispatch($participant->getEvents());
            $this->eventStorage->add($participant);
        }
    }


    private function showParticipants()
    {

    }
}
