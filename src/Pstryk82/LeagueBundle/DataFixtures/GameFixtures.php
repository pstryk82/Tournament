<?php

namespace Pstryk82\LeagueBundle\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Pstryk82\LeagueBundle\Domain\Aggregate\Game;
use Pstryk82\LeagueBundle\Scheduler\LeagueScheduler;

class GameFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $league = $this->getReference('League');
        $participants = [];
        for ($participantId = 101; $participantId <= 108; $participantId++) {
            $participants[$participantId] = $this->getReference('Participant-' . $participantId);
        }
        $scheduler = new LeagueScheduler();
        $schedule = $scheduler->generateSchedule($participants, $league);
        foreach ($schedule as $round) {
            foreach ($round as $game) {
                $this->generateGameResults($game);
            }
        }
    }

    private function generateGameResults(Game $game)
    {
//        $game->getHomeParticipant()->getTeam()->getRank()
        $game->recordResult(mt_rand(0, 3), mt_rand(0, 3));
    }

    public function getDependencies()
    {
        return [
            LeagueFixtures::class,
            ParticipantFixtures::class
        ];
    }
}
