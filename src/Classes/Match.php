<?php

namespace rkistaps\Engine\Classes;

use rkistaps\Engine\Exceptions\EngineException;
use rkistaps\Engine\Helpers\LineupHelper;
use rkistaps\Engine\Structures\Coach;
use rkistaps\Engine\Structures\Event;
use rkistaps\Engine\Structures\MatchSettings;
use rkistaps\Engine\Structures\MatchReport;
use rkistaps\Engine\Structures\Player;
use rkistaps\Engine\Structures\Possession;
use rkistaps\Engine\Structures\ShootConfig;
use rkistaps\Engine\Structures\ShootResult;
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
        $this->report = new MatchReport();
    }

    /**
     * Play match
     *
     * @return MatchReport
     * @throws EngineException
     */
    public function play(): MatchReport
    {
        if ($this->isPlayed) {
            throw new EngineException('Match already played');
        }

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

        $htDefStr = $homeTeamStrength->getDefense();
        $htMidStr = $homeTeamStrength->getMidfield();
        $htAttStr = $homeTeamStrength->getAttack();

        $atDefStr = $awayTeamStrength->getDefense();
        $atMidStr = $awayTeamStrength->getMidfield();
        $atAttStr = $awayTeamStrength->getAttack();

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
            for ($i = $this->homeTeamShootCount; $i > 0; $i--) {
                $config = $this->buildShootConfig($this->homeTeam, $this->awayTeam);
                $shootResult = $this->shoot($config);

                if ($shootResult->isGoal()) {
                    $this->report->homeScore += 1;
                }
            }
        }

        // away team shoots
        if ($this->homeTeamShootCount) {
            for ($i = $this->homeTeamShootCount; $i > 0; $i--) {
                $config = $this->buildShootConfig($this->awayTeam, $this->homeTeam);
                $shootResult = $this->shoot($config);

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
     * Calculate shoot result
     *
     * @param ShootConfig $shootConfig
     * @return ShootResult
     */
    private function shoot(ShootConfig $shootConfig): ShootResult
    {
        $strikerPref = $shootConfig->striker->getPerformance();
        $goalkeeperPref = $shootConfig->goalkeeper->getPerformance();

        $attackHelperPref = $shootConfig->attackHelper ? $shootConfig->attackHelper->getPerformance() : 0;
        $defenseHelperPref = $shootConfig->defenseHelper ? $shootConfig->defenseHelper->getPerformance() : 0;

        $goalK = round($strikerPref * $strikerPref / ($strikerPref * $strikerPref + $goalkeeperPref * $goalkeeperPref), 2);

        $helperBonus = 0;
        if ($shootConfig->attackHelper && $shootConfig->defenseHelper) { // both helpers
            $helperBonus = round(($attackHelperPref / ($attackHelperPref + $defenseHelperPref) - 0.5), 2);
        } elseif ($shootConfig->attackHelper && !$shootConfig->defenseHelper) {
            $helperBonus = round($attackHelperPref / ($goalkeeperPref * 2.5), 2);
        } elseif (!$shootConfig->attackHelper && $shootConfig->defenseHelper) {
            $helperBonus = round($defenseHelperPref / ($strikerPref * 2.5), 2);
        }

        $goalK += $helperBonus;
        $saveK = round(0.5 + rand(-7, 7) / 100, 2);

        $goal = $goalK - $this->saveBonus * 0.05 > $saveK;

        if ($goal) {
            if ($this->saveBonus <= -2) {
                $this->saveBonus = 0;
            } else {
                $this->saveBonus -= 1;
            }
        } else {
            if ($this->saveBonus >= 2) {
                $this->saveBonus = 0;
            } else {
                $this->saveBonus += 1;
            }
        }

        $resultType = $goal ? ShootResult::RESULT_GOAL : ShootResult::RESULT_SAVE;
        $result = new ShootResult($resultType, $shootConfig);

        return $result;
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