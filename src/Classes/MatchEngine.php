<?php

namespace rkistaps\Engine\Classes;

use rkistaps\Engine\Exceptions\EngineException;
use rkistaps\Engine\Structures\MatchSettings;
use rkistaps\Engine\Structures\Squad;

class MatchEngine
{
    /** @var MatchSettings */
    private $settings;

    /**
     * MatchEngine constructor.
     *
     * @param MatchSettings $settings
     */
    public function __construct(MatchSettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Play match
     *
     * @param Squad $homeTeam
     * @param Squad $awayTeam
     * @return Match
     * @throws EngineException
     */
    public function play(Squad $homeTeam, Squad $awayTeam): Match
    {
        $match = new Match($homeTeam, $awayTeam, $this->settings);

        $match->play();

        return $match;
    }
}
