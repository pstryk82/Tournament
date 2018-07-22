<?php

namespace Pstryk82\LeagueBundle\Tests\Domain\Logic;

use Pstryk82\LeagueBundle\Domain\Aggregate\Game;
use Pstryk82\LeagueBundle\Domain\Aggregate\LeagueParticipant;
use Pstryk82\LeagueBundle\Domain\Exception\GameOutcomeResolverException;
use Pstryk82\LeagueBundle\Domain\Logic\GameOutcomeResolver;

class GameOutcomeResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var GameOutcomeResolver
     */
    private $resolver;

    /**
     * @var LeagueParticipant
     */
    private $homeParticipant;

    /**
     * @var LeagueParticipant
     */
    private $awayParticipant;


    public function setUp()
    {
        $this->homeParticipant = new LeagueParticipant('home');
        $this->awayParticipant = new LeagueParticipant('away');
        $this->resolver = new GameOutcomeResolver();
    }

    public function tearDown()
    {
        unset($this->homeParticipant, $this->awayParticipant);
    }

    public function testHomeWin()
    {
        $gameId = 123;
        $game = new Game($gameId);
        $game
            ->setHomeParticipant($this->homeParticipant)
            ->setAwayParticipant($this->awayParticipant)
            ->setHomeScore(2)
            ->setAwayScore(1)
            ->setPlayed(1);

        $this->resolver->determine($game);

        $this->assertEquals($this->homeParticipant, $this->resolver->getWinner());
        $this->assertEquals($this->awayParticipant, $this->resolver->getLoser());
        $this->assertEquals($game->getHomeScore(), $this->resolver->getWinnerScore());
        $this->assertEquals($game->getAwayScore(), $this->resolver->getLoserScore());
        $this->assertFalse($this->resolver->isDraw());
    }

    public function testAwayWin()
    {
        $gameId = 123;
        $game = new Game($gameId);
        $game
            ->setHomeParticipant($this->homeParticipant)
            ->setAwayParticipant($this->awayParticipant)
            ->setHomeScore(0)
            ->setAwayScore(4)
            ->setPlayed(1);

        $this->resolver->determine($game);

        $this->assertEquals($this->awayParticipant, $this->resolver->getWinner());
        $this->assertEquals($this->homeParticipant, $this->resolver->getLoser());
        $this->assertEquals($game->getAwayScore(), $this->resolver->getWinnerScore());
        $this->assertEquals($game->getHomeScore(), $this->resolver->getLoserScore());
        $this->assertFalse($this->resolver->isDraw());
    }

    public function testDraw()
    {
        $gameId = 123;
        $game = new Game($gameId);
        $game
            ->setHomeParticipant($this->homeParticipant)
            ->setAwayParticipant($this->awayParticipant)
            ->setHomeScore(2)
            ->setAwayScore(2)
            ->setPlayed(1);

        $this->resolver->determine($game);

        $this->assertTrue($this->resolver->isDraw());
        $this->assertNull($this->resolver->getWinner());
        $this->assertNull($this->resolver->getLoser());
        
        $this->assertEquals($game->getAwayScore(), $this->resolver->getDrawScore());
        $this->assertEquals($game->getHomeScore(), $this->resolver->getDrawScore());
    }

    public function testUnableToDetermine()
    {
        $gameId = 123;
        $game = new Game($gameId);
        $game
            ->setHomeParticipant($this->homeParticipant)
            ->setAwayParticipant($this->awayParticipant);

        $this->expectException(GameOutcomeResolverException::class);
        $this->resolver->determine($game);
    }
}
