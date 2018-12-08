<?php

namespace rkistaps\Engine\Classes;

use rkistaps\Engine\Exceptions\EngineException;
use rkistaps\Engine\Structures\MatchSettings;
use rkistaps\Engine\Structures\MatchResult;
use rkistaps\Engine\Structures\Squad;

class Match
{
    /** @var Squad */
    private $homeTeam;

    /** @var Squad */
    private $awayTeam;

    /** @var MatchSettings */
    private $settings;

    /**
     * @var MatchResult
     */
    private $result;

    /** @var bool */
    private $isPlayed = false;

    /**
     * Match constructor.
     *
     * @param Squad $homeTeam
     * @param Squad $awayTeam
     * @param MatchSettings $settings
     */
    public function __construct(Squad $homeTeam, Squad $awayTeam, MatchSettings $settings)
    {
        $this->homeTeam = $homeTeam;
        $this->awayTeam = $awayTeam;
        $this->settings = $settings;
    }

    /**
     * Play match
     *
     * @return MatchResult
     * @throws EngineException
     */
    public function play(): MatchResult
    {
        if ($this->isPlayed) {
            throw new EngineException('Match already played');
        }

        $this->result = new MatchResult();

        $this->homeTeam->perform();
        $this->awayTeam->perform();

        $this->isPlayed = true;

        return $this->result;
    }

    /**
     * Get match result
     *
     * @return MatchResult
     * @throws EngineException
     */
    public function getResult(): MatchResult
    {
        if (!$this->isPlayed) {
            throw new EngineException('Match not played');
        }

        return $this->result;
    }
}