<?php

namespace rkistaps\Engine\Classes;

use rkistaps\Engine\Exceptions\EngineException;
use rkistaps\Engine\Structures\Coach;
use rkistaps\Engine\Structures\MatchSettings;
use rkistaps\Engine\Structures\MatchResult;
use rkistaps\Engine\Structures\Possession;
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

    /** @var Possession */
    private $possession;

    /** @var int */
    private $homeTeamAttackCount = 0;

    /** @var int */
    private $awayTeamAttackCount = 0;

    /** @var PossessionCalculator */
    private $possessionCalculator;

    /**
     * Match constructor.
     *
     * @param Team $homeTeam
     * @param Team $awayTeam
     * @param MatchSettings $settings
     * @param PossessionCalculator $possessionCalculator
     */
    public function __construct(Team $homeTeam, Team $awayTeam, MatchSettings $settings, PossessionCalculator $possessionCalculator)
    {
        $this->homeTeam = $homeTeam;
        $this->awayTeam = $awayTeam;
        $this->settings = $settings;
        $this->possessionCalculator = $possessionCalculator;
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

        $this->possession = $this->possessionCalculator->calculate($this->homeTeam->getStrength(), $this->awayTeam->getStrength());

        $possession = $this->possession;
        $baseAttackCount = $this->settings->baseAttackCount;
        $attackCountRandomModifier = rand(100 - $this->settings->attackCountRandomModifier, 100 + $this->settings->attackCountRandomModifier) / 100;

        $this->homeTeamAttackCount = round($baseAttackCount * $possession->homeTeam * $attackCountRandomModifier);
        $this->awayTeamAttackCount = round($baseAttackCount * $possession->awayTeam * $attackCountRandomModifier);

        // TODO add additional attack count calculation



        $this->isPlayed = true;

        return $this->result;
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

        // modify by coach
        $this->modifyStrengthByCoach($this->homeTeam);
        $this->modifyStrengthByCoach($this->awayTeam);

        $this->homeTeam->getStrength()->applyTactic($this->homeTeam->getTactic());
        $this->awayTeam->getStrength()->applyTactic($this->awayTeam->getTactic());
    }

    /**
     * Modify team strength based on coach
     *
     * @param Team $team
     */
    public function modifyStrengthByCoach(Team $team)
    {
        $coach = $team->getCoach();
        if (!$coach) {
            return;
        }
        $levelBonus = $this->settings->coachLevelBonus * $coach->getLevel();

        $defenseModifier = $levelBonus;
        $midfieldModifier = $levelBonus;
        $attackModifier = $levelBonus;

        if ($coach->getSpeciality() == Coach::SPECIALITY_DEF) {
            $defenseModifier *= $this->settings->coachSpecialityBonus;
        } elseif ($coach->getSpeciality() == Coach::SPECIALITY_MID) {
            $midfieldModifier *= $this->settings->coachSpecialityBonus;
        } elseif ($coach->getSpeciality() == Coach::SPECIALITY_ATT) {
            $attackModifier *= $this->settings->coachSpecialityBonus;
        }

        $modifier = new SquadStrengthModifier();
        $modifier->defenseModifier = $defenseModifier;
        $modifier->midfieldModifier = $midfieldModifier;
        $modifier->attackModifier = $attackModifier;

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