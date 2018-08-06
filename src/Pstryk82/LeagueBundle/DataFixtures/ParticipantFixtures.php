<?php

namespace Pstryk82\LeagueBundle\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\LeagueParticipantProjection;

class ParticipantFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $league = $this->getReference('League');
        $participantId = 100;
        for ($teamId = 1; $teamId <= 8; $teamId++) {
            $team = $this->getReference('Team-' . $teamId);
            $participant = new LeagueParticipantProjection(++$participantId);
            $participant
                ->setTeam($team)
                ->setCompetition($league);
            $manager->persist($participant);
            $this->addReference('Participant-' . $participantId, $participant);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            LeagueFixtures::class,
            TeamFixtures::class
        ];
    }
}
