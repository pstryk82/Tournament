<?php

namespace Pstryk82\LeagueBundle\Domain\Aggregate;

use Pstryk82\LeagueBundle\Domain\Exception\GameLogicException;
use Pstryk82\LeagueBundle\Domain\Logic\GameOutcomeResolver;
use Pstryk82\LeagueBundle\Event\GameWasPlanned;
use Pstryk82\LeagueBundle\Event\GameWasPlayed;
use Pstryk82\LeagueBundle\EventEngine\EventSourced;
use Pstryk82\LeagueBundle\Generator\IdGenerator;

class Game implements AggregateInterface
{
    use EventSourced;

    /**
     * @var string
     */
    private $aggregateId;

    /**
     * @var bool
     */
    private $played = false;

    /**
     * @var AbstractParticipant
     */
    private $homeParticipant;

    /**
     * @var AbstractParticipant
     */
    private $awayParticipant;

    /**
     * @var int
     */
    private $homeScore;

    /**
     * @var int
     */
    private $awayScore;

    /**
     * @var Competition
     */
    private $competition;

    /**
     * @var bool
     */
    private $onNeutralGround = false;

    /**
     * @var \DateTime
     */
    private $beginningTime;

    /**
     * @var string
     */
    private $round;

    /**
     * @param string $aggregateId
     */
    public function __construct($aggregateId)
    {
        $this->aggregateId = $aggregateId;
    }

    /**
     * @throws GameLogicException
     */
    public static function create(
        AbstractParticipant $homeParticipant,
        AbstractParticipant $awayParticipant,
        Competition $competition,
        \DateTime $beginningTime,
        $round,
        $onNeutralGround = false
    ): self
    {
        if ($homeParticipant->getAggregateId() == $awayParticipant->getAggregateId()) {
            throw new GameLogicException(
                'A team cannot play against itself: aggregateId = ' . $homeParticipant->getAggregateId()
            );
        }
        $game = new self($aggregateId = IdGenerator::generate());
        $game
            ->setHomeParticipant($homeParticipant)
            ->setAwayParticipant($awayParticipant)
            ->setCompetition($competition)
            ->setBeginningTime($beginningTime)
            ->setRound($round)
            ->setOnNeutralGround($onNeutralGround);

        $gameWasPlannedEvent = new GameWasPlanned(
            $aggregateId,
            $homeParticipant,
            $awayParticipant,
            $competition,
            $beginningTime,
            new \DateTime(),
            $round,
            $onNeutralGround
        );

        $game->recordThat($gameWasPlannedEvent);

        return $game;
    }

    private function applyGameWasPlanned(GameWasPlanned $event)
    {
        $this
            ->setHomeParticipant($event->getHomeParticipant())
            ->setAwayParticipant($event->getAwayParticipant())
            ->setCompetition($event->getCompetition())
            ->setBeginningTime($event->getBeginningTime())
            ->setRound($event->getRound())
            ->setOnNeutralGround($event->getOnNeutralGround())
        ;
    }

    public function recordResult(int $homeScore, int $awayScore)
    {
        $gameWasPlayedEvent = new GameWasPlayed($this->aggregateId, $homeScore, $awayScore, new \DateTime());
        $this->recordThat($gameWasPlayedEvent);
        $this->apply($gameWasPlayedEvent);

        $gameOutcomeResolver = new GameOutcomeResolver();
        $gameOutcomeResolver->determine($this);
        if ($gameOutcomeResolver->isDraw()) {
            $this->homeParticipant->recordPointsForDraw($this, $gameOutcomeResolver->getDrawScore());
            $this->awayParticipant->recordPointsForDraw($this, $gameOutcomeResolver->getDrawScore());
        } else {
            $winner = $gameOutcomeResolver->getWinner();
            $winner->recordPointsForWin($this, $gameOutcomeResolver);
            $loser = $gameOutcomeResolver->getLoser();
            $loser->recordPointsForLose($this, $gameOutcomeResolver);
        }
    }

    private function applyGameWasPlayed(GameWasPlayed $event)
    {
        $this
            ->setHomeScore($event->getHomeScore())
            ->setAwayScore($event->getAwayScore())
            ->setPlayed($event->getPlayed());
    }
    
    public function getAggregateId()
    {
        return $this->aggregateId;
    }

    /**
     * Set played.
     *
     * @param bool $played
     *
     * @return Game
     */
    public function setPlayed($played)
    {
        $this->played = $played;

        return $this;
    }

    /**
     * Get played.
     *
     * @return bool
     */
    public function getPlayed()
    {
        return $this->played;
    }

    /**
     * Set homeTeam.
     *
     * @param AbstractParticipant $homeParticipant
     *
     * @return Game
     */
    public function setHomeParticipant(AbstractParticipant $homeParticipant)
    {
        $this->homeParticipant = $homeParticipant;

        return $this;
    }

    /**
     * Get homeTeam.
     *
     * @return AbstractParticipant
     */
    public function getHomeParticipant()
    {
        return $this->homeParticipant;
    }

    /**
     * Set awayTeam.
     *
     * @param AbstractParticipant $awayParticipant
     *
     * @return Game
     */
    public function setAwayParticipant(AbstractParticipant $awayParticipant)
    {
        $this->awayParticipant = $awayParticipant;

        return $this;
    }

    /**
     * Get awayTeam.
     *
     * @return AbstractParticipant
     */
    public function getAwayParticipant()
    {
        return $this->awayParticipant;
    }

    /**
     * Set homeTeamScore.
     *
     * @param int $homeScore
     *
     * @return Game
     */
    public function setHomeScore($homeScore)
    {
        $this->homeScore = $homeScore;

        return $this;
    }

    /**
     * Get homeTeamScore.
     *
     * @return int
     */
    public function getHomeScore()
    {
        return $this->homeScore;
    }

    /**
     * Set awayTeamScore.
     *
     * @param int $awayScore
     *
     * @return Game
     */
    public function setAwayScore($awayScore)
    {
        $this->awayScore = $awayScore;

        return $this;
    }

    /**
     * Get awayTeamScore.
     *
     * @return int
     */
    public function getAwayScore()
    {
        return $this->awayScore;
    }

    /**
     * Set competition.
     *
     * @param Competition $competition
     *
     * @return Game
     */
    public function setCompetition(Competition $competition)
    {
        $this->competition = $competition;

        return $this;
    }

    /**
     * Get competition.
     *
     * @return Competition
     */
    public function getCompetition()
    {
        return $this->competition;
    }

    /**
     * Set onNeutralGround.
     *
     * @param bool $onNeutralGround
     *
     * @return Game
     */
    public function setOnNeutralGround($onNeutralGround)
    {
        $this->onNeutralGround = $onNeutralGround;

        return $this;
    }

    /**
     * Get onNeutralGround.
     *
     * @return bool
     */
    public function getOnNeutralGround()
    {
        return $this->onNeutralGround;
    }

    /**
     * Set beginningTime.
     *
     * @param \DateTime $beginningTime
     *
     * @return Game
     */
    public function setBeginningTime($beginningTime)
    {
        $this->beginningTime = $beginningTime;

        return $this;
    }

    /**
     * Get beginningTime.
     *
     * @return \DateTime
     */
    public function getBeginningTime()
    {
        return $this->beginningTime;
    }

    public function getRound(): string
    {
        return $this->round;
    }

    public function setRound($round): self
    {
        $this->round = $round;

        return $this;
    }


}
