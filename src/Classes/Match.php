<?php

namespace rkistaps\Engine\Classes;

use rkistaps\Engine\Exceptions\EngineException;
use rkistaps\Engine\Structures\Coach;
use rkistaps\Engine\Structures\MatchSettings;
use rkistaps\Engine\Structures\MatchResult;
use rkistaps\Engine\Structures\SquadStrengthModifier;
use rkistaps\Engine\Structures\Team;

class Match
{
    /** @var Team */
    private $homeTeam;

    /** @var Team */
    private $awayTeam;

    /** @var MatchSettings */
    private $settings;

    /** @var MatchResult */
    private $result;

    /** @var bool */
    private $isPlayed = false;

    /** @var float */
    private $homeTeamPossession = 0;

    /** @var float */
    private $awayTeamPossession = 0;

    /**
     * Match constructor.
     *
     * @param Team $homeTeam
     * @param Team $awayTeam
     * @param MatchSettings $settings
     */
    public function __construct(Team $homeTeam, Team $awayTeam, MatchSettings $settings)
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

        $this->homeTeam->perform($this->settings->performanceRandomRange);
        $this->awayTeam->perform($this->settings->performanceRandomRange);
        $this->modifyStrengths();

        $this->calculatePossessions();

        $this->isPlayed = true;

        return $this->result;
    }

    /**
     * Calculate ball possession
     */
    private function calculatePossessions()
    {

    }

    /**
     * Modify team strengths based on multiple factors
     */
    private function modifyStrengths()
    {
        if ($this->settings->hasHomeTeamBonus) {
            $modifier = new SquadStrengthModifier();
            $modifier->defenseModifier = $this->settings->homeTeamBonus;
            $modifier->midfieldModifier = $this->settings->homeTeamBonus;
            $modifier->attackModifier = $this->settings->homeTeamBonus;
            $this->homeTeam->getStrength()->modify($modifier);
        }

        $this->modifyStrengthByCoach($this->homeTeam);
        $this->modifyStrengthByCoach($this->awayTeam);
    }

    /**
     * Modify team strength based on coach
     *
     * @param Team $team
     */
    public function modifyStrengthByCoach(Team $team)
    {
        $coach = $team->getCoach();
        if(!$coach){
            return;
        }

        $modifier = new SquadStrengthModifier();

        if ($coach->getSpeciality() == Coach::SPECIALITY_DEF) {
            $modifier->defenseModifier = $this->settings->coachSpecialityBonus;
        } elseif ($coach->getSpeciality() == Coach::SPECIALITY_MID) {
            $modifier->midfieldModifier = $this->settings->coachSpecialityBonus;
        } elseif ($coach->getSpeciality() == Coach::SPECIALITY_ATT) {
            $modifier->attackModifier = $this->settings->coachSpecialityBonus;
        }

        $team->getStrength()->modify($modifier);
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