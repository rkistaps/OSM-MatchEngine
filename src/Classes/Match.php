<?php

namespace rkistaps\Engine\Classes;

use rkistaps\Engine\Exceptions\EngineException;
use rkistaps\Engine\Helpers\LineupHelper;
use rkistaps\Engine\Structures\Coach;
use rkistaps\Engine\Structures\Event;
use rkistaps\Engine\Structures\FlatSquadStrengthModifier;
use rkistaps\Engine\Structures\MatchSettings;
use rkistaps\Engine\Structures\MatchReport;
use rkistaps\Engine\Structures\Player;
use rkistaps\Engine\Structures\Possession;
use rkistaps\Engine\Structures\RelativeSquadStrengthModifier;
use rkistaps\Engine\Structures\ShootConfig;
use rkistaps\Engine\Structures\Team;

class Match
{
    /** @var Team */
    private $homeTeam;

    /** @var Team */
    private $awayTeam;

    /** @var MatchSettings */
    private $settings;

    /** @var MatchReport */
    private $report;

    /** @var bool */
    private $isPlayed = false;

    /** @var Possession */
    private $possession;

    /** @var int */
    private $homeTeamAttackCount = 0;

    /** @var int */
    private $awayTeamAttackCount = 0;

    /** @var int */
    private $homeTeamShootCount = 0;

    /** @var int */
    private $awayTeamShootCount = 0;

    /** @var PossessionCalculator */
    private $possessionCalculator;

    /** @var int */
    private $saveBonus = 0;

    /** @var ShootEngine */
    private $shootEngine;


    /**
     * Match constructor.
     *
     * @param MatchSettings $settings
     * @param PossessionCalculator $possessionCalculator
     * @param MatchReport $matchReport
     * @param ShootEngine $shootEngine
     */
    public function __construct(MatchSettings $settings, PossessionCalculator $possessionCalculator, MatchReport $matchReport, ShootEngine $shootEngine)
    {
        $this->settings = $settings;
        $this->possessionCalculator = $possessionCalculator;
        $this->report = $matchReport;
        $this->shootEngine = $shootEngine;
    }

    /**
     * Play match
     *
     * @param Team $homeTeam
     * @param Team $awayTeam
     * @return MatchReport
     * @throws EngineException
     */
    public function play(Team $homeTeam, Team $awayTeam): MatchReport
    {
        if ($this->isPlayed) {
            throw new EngineException('Match already played');
        }

        $this->homeTeam = $homeTeam;
        $this->awayTeam = $awayTeam;

        $this->homeTeam->perform($this->settings->performanceRandomRange);
        $this->awayTeam->perform($this->settings->performanceRandomRange);
        $this->modifyStrengths();

        $homeTeamStrength = $this->homeTeam->getStrength();
        $awayTeamStrength = $this->awayTeam->getStrength();

        $this->possession = $this->possessionCalculator->calculate($homeTeamStrength, $awayTeamStrength);

        $possession = $this->possession;
        $baseAttackCount = $this->settings->baseAttackCount;
        $attackCountRandomModifier = rand(100 - $this->settings->attackCountRandomModifier, 100 + $this->settings->attackCountRandomModifier) / 100;

        $this->homeTeamAttackCount = round($baseAttackCount * $possession->homeTeam * $attackCountRandomModifier);
        $this->awayTeamAttackCount = round($baseAttackCount * $possession->awayTeam * $attackCountRandomModifier);

        // TODO add additional attack count calculation

        $htDefStr = $homeTeamStrength->defence;
        $htMidStr = $homeTeamStrength->midfield;
        $htAttStr = $homeTeamStrength->attack;

        $atDefStr = $awayTeamStrength->defence;
        $atMidStr = $awayTeamStrength->midfield;
        $atAttStr = $awayTeamStrength->attack;

        $this->homeTeamShootCount = round(($htAttStr + $htMidStr * 0.33) / ($atDefStr + $atMidStr * 0.33) * $this->homeTeamAttackCount);
        $this->awayTeamShootCount = round(($atAttStr + $atMidStr * 0.33) / ($htDefStr + $htMidStr * 0.33) * $this->awayTeamAttackCount);

        $this->homeTeamShootCount = $this->homeTeamShootCount < $this->homeTeamAttackCount ? $this->homeTeamShootCount : $this->homeTeamAttackCount;
        $this->awayTeamShootCount = $this->awayTeamShootCount < $this->awayTeamAttackCount ? $this->awayTeamShootCount : $this->awayTeamAttackCount;

        $htAttacksStopped = $this->homeTeamAttackCount - $this->homeTeamShootCount;
        if ($htAttacksStopped) {
            $this->addAttackStopEvents($htAttacksStopped, $this->homeTeam, $this->awayTeam);
        }

        $atAttacksStopped = $this->awayTeamAttackCount - $this->awayTeamShootCount;
        if ($atAttacksStopped) {
            $this->addAttackStopEvents($atAttacksStopped, $this->awayTeam, $this->homeTeam);
        }

        // home team shoots
        if ($this->homeTeamShootCount) {
            $saveBonus = 0;
            for ($i = $this->homeTeamShootCount; $i > 0; $i--) {
                $config = $this->buildShootConfig($this->homeTeam, $this->awayTeam);
                $config->saveBonus = $saveBonus;
                $shootResult = $this->shootEngine->shoot($config);

                if ($shootResult->isGoal()) {
                    $saveBonus = ($saveBonus <= -2) ? 0 : $saveBonus + 1;
                } else {
                    $saveBonus = ($saveBonus >= 2) ? 0 : $saveBonus - 1;
                }

                if ($shootResult->isGoal()) {
                    $this->report->homeScore += 1;
                }
            }
        }

        // away team shoots
        if ($this->awayTeamShootCount) {
            $saveBonus = 0;
            for ($i = $this->awayTeamShootCount; $i > 0; $i--) {
                $config = $this->buildShootConfig($this->awayTeam, $this->homeTeam);
                $config->saveBonus = $saveBonus;
                $shootResult = $this->shootEngine->shoot($config);

                if ($shootResult->isGoal()) {
                    $saveBonus = ($saveBonus <= -2) ? 0 : $saveBonus + 1;
                } else {
                    $saveBonus = ($saveBonus >= 2) ? 0 : $saveBonus - 1;
                }

                if ($shootResult->isGoal()) {
                    $this->report->awayScore += 1;
                }
            }
        }

        $this->isPlayed = true;

        return $this->report;
    }

    /**
     * Get shoot config
     *
     * @param Team $attackingTeam
     * @param Team $defendingTeam
     * @return ShootConfig
     */
    private function buildShootConfig(Team $attackingTeam, Team $defendingTeam): ShootConfig
    {
        $rand = rand(1, 100);
        $pos = Player::POS_F;
        if ($rand > 60 and $rand <= 85) {
            $pos = Player::POS_M;
        } elseif ($rand > 85) {
            $pos = Player::POS_D;
        }

        $striker = LineupHelper::getRandomPlayerInPosition($attackingTeam->getLineup(), $pos, 1);
        $goalkeeper = LineupHelper::getRandomPlayerInPosition($defendingTeam->getLineup(), Player::POS_G, 1);

        $config = new ShootConfig();
        $config->striker = $striker;
        $config->goalkeeper = $goalkeeper;

        return $config;
    }

    /**
     * Add attack stop events to match report
     *
     * @param int $count
     * @param Team $attackingTeam
     * @param Team $defendingTeam
     */
    private function addAttackStopEvents(int $count, Team $attackingTeam, Team $defendingTeam)
    {
        for ($i = $count; $i > 0; $i--) {
            $minute = rand(1, 93);

            $data = [
                'attacking_team' => $attackingTeam->getId(),
                'defending_team' => $defendingTeam->getId(),
            ];

            $event = new Event(Event::TYPE_TACKLE, $minute, $data);
            $this->report->addEvent($event);
        }
    }

    /**
     * Modify team strengths based on multiple factors
     */
    private function modifyStrengths()
    {
        if ($this->settings->hasHomeTeamBonus) {
            $modifier = new RelativeSquadStrengthModifier();
            $modifier->defenseModifier = $this->settings->homeTeamBonus;
            $modifier->midfieldModifier = $this->settings->homeTeamBonus;
            $modifier->attackModifier = $this->settings->homeTeamBonus;
            $this->homeTeam->getStrength()->applyModifier($modifier);
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

        $modifier = new FlatSquadStrengthModifier();
        $modifier->defenseModifier = $defenseModifier;
        $modifier->midfieldModifier = $midfieldModifier;
        $modifier->attackModifier = $attackModifier;

        $team->getStrength()->applyModifier($modifier);
    }

    /**
     * Get match result
     *
     * @return MatchReport
     * @throws EngineException
     */
    public function getResult(): MatchReport
    {
        if (!$this->isPlayed) {
            throw new EngineException('Match not played');
        }

        return $this->report;
    }
}