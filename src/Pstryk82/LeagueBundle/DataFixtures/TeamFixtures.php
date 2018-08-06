<?php

namespace Pstryk82\LeagueBundle\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\TeamProjection;

class TeamFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $teamData = json_decode(
            file_get_contents('./src/Pstryk82/LeagueBundle/DataFixtures/teams_8.json')
        );

        $id = 0;
        foreach ($teamData as $teamRecord) {
            $id++;
            $team = new TeamProjection($id);
            $team
                ->setName($teamRecord->name)
                ->addRank($teamRecord->rank)
                ->setStadium($teamRecord->stadium);
            $this->addReference('Team-' . $id, $team);
            $manager->persist($team);
        }
        $manager->flush();
    }

}
