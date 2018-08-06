<?php

namespace Pstryk82\LeagueBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Pstryk82\LeagueBundle\Domain\ReadModel\Projection\LeagueProjection;

class LeagueFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $league = new LeagueProjection('league');
        $league
            ->setName('Fixtured League')
            ->setSeason('2018/2019')
            ->setRankPointsForWin(5)
            ->setRankPointsForDraw(2)
            ->setRankPointsForLose(0)
            ->setPointsForWin(3)
            ->setPointsForDraw(1)
            ->setPointsForLose(0)
            ->setNumberOfLegs(2);
        $manager->persist($league);
        $manager->flush();

        $this->addReference('League', $league);
    }

}
