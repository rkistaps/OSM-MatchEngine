<?php

namespace rkistaps\Engine\Classes;

use rkistaps\Engine\Exceptions\EngineException;
use rkistaps\Engine\Structures\MatchSettings;
use rkistaps\Engine\Structures\Team;

class MatchEngine
{
    /** @var MatchSettings */
    private $settings;

    /** @var PossessionCalculator */
    private $possessionCalculator;

    /**
     * MatchEngine constructor.
     *
     * @param MatchSettings $settings
     * @param PossessionCalculator $calculator
     */
    public function __construct(MatchSettings $settings, PossessionCalculator $calculator)
    {
        $this->settings = $settings;
        $this->possessionCalculator= $calculator;
    }

    /**
     * Play match
     *
     * @param Team $homeTeam
     * @param Team $awayTeam
     * @return Match
     * @throws EngineException
     */
    public function play(Team $homeTeam, Team $awayTeam): Match
    {
        $match = new Match($homeTeam, $awayTeam, $this->settings, $this->possessionCalculator);

        $match->play();

        return $match;
    }
}
