<?php

namespace OSM\MatchEngine\Classes;

use OSM\MatchEngine\Helpers\LineupHelper;
use OSM\MatchEngine\Structures\Coach;
use OSM\MatchEngine\Structures\Event;
use OSM\MatchEngine\Structures\FlatSquadStrengthModifier;
use OSM\MatchEngine\Structures\MatchSettings;
use OSM\MatchEngine\Structures\MatchReport;
use OSM\MatchEngine\Structures\Player;
use OSM\MatchEngine\Structures\RelativeSquadStrengthModifier;
use OSM\MatchEngine\Structures\ShootConfig;
use OSM\MatchEngine\Structures\SquadStrength;
use OSM\MatchEngine\Structures\Team;

class Match
{
    /** @var PossessionCalculator */
    private $possessionCalculator;

    /** @var ShootEngine */
    private $shootEngine;

    /**
     * Match constructor.
     *
     * @param PossessionCalculator $possessionCalculator
     * @param ShootEngine $shootEngine
     */
    public function __construct(PossessionCalculator $possessionCalculator, ShootEngine $shootEngine)
    {
        $this->possessionCalculator = $possessionCalculator;
        $this->shootEngine = $shootEngine;
    }

    /**
     * Play match
     *
     * @param Team $homeTeam
     * @param Team $awayTeam
     * @param MatchSettings $settings
     * @return MatchReport
     */
    public function play(Team $homeTeam, Team $awayTeam, MatchSettings $settings): MatchReport
    {
        $report = new MatchReport();

        $homeTeamStrength = $homeTeam->perform($settings->performanceRandomRange);
        $awayTeamStrength = $awayTeam->perform($settings->performanceRandomRange);

        $this->modifyStrengths($homeTeam, $settings);
        $this->modifyStrengths($awayTeam, $settings);

        if ($settings->hasHomeTeamBonus) {
            $this->applyHomeTeamBonus($homeTeamStrength, $settings);
        }

        $possession = $this->possessionCalculator->calculate($homeTeamStrength, $awayTeamStrength);
        $report->possession = $possession;

        $baseAttackCount = $settings->baseAttackCount;
        $attackCountRandomModifier = rand(100 - $settings->attackCountRandomModifier, 100 + $settings->attackCountRandomModifier) / 100;

        $report->homeTeamAttackCount = round($baseAttackCount * $possession->homeTeam * $attackCountRandomModifier);
        $report->awayTeamAttackCount = round($baseAttackCount * $possession->awayTeam * $attackCountRandomModifier);

        // TODO add additional attack count calculation

        $htDefStr = $homeTeamStrength->defence;
        $htMidStr = $homeTeamStrength->midfield;
        $htAttStr = $homeTeamStrength->attack;

        $atDefStr = $awayTeamStrength->defence;
        $atMidStr = $awayTeamStrength->midfield;
        $atAttStr = $awayTeamStrength->attack;

        $report->homeTeamShootCount = round(($htAttStr + $htMidStr * 0.33) / ($atDefStr + $atMidStr * 0.33) * $report->homeTeamAttackCount);
        $report->awayTeamShootCount = round(($atAttStr + $atMidStr * 0.33) / ($htDefStr + $htMidStr * 0.33) * $report->awayTeamAttackCount);

        $report->homeTeamShootCount = $report->homeTeamShootCount > $report->homeTeamAttackCount ? $report->homeTeamAttackCount : $report->homeTeamShootCount;
        $report->awayTeamShootCount = $report->awayTeamShootCount > $report->awayTeamAttackCount ? $report->awayTeamAttackCount : $report->awayTeamShootCount;

        $htAttacksStopped = $report->homeTeamAttackCount - $report->homeTeamShootCount;
        if ($htAttacksStopped) {
            $this->addAttackStopEvents($htAttacksStopped, $homeTeam, $awayTeam, $report);
        }

        $atAttacksStopped = $report->awayTeamAttackCount - $report->awayTeamShootCount;
        if ($atAttacksStopped) {
            $this->addAttackStopEvents($atAttacksStopped, $awayTeam, $homeTeam, $report);
        }

        // home team shoots
        if ($report->homeTeamShootCount) {
            $events = $this->processShoots($homeTeam, $awayTeam, $report->homeTeamShootCount);
            $report->addEvents($events);

            foreach ($events as $event) {
                if ($event->getType() == Event::TYPE_GOAL) {
                    $report->homeScore += 1;
                }
            }
        }

        // away team shoots
        if ($report->awayTeamShootCount) {
            $events = $this->processShoots($awayTeam, $homeTeam, $report->awayTeamShootCount);
            $report->addEvents($events);

            foreach ($events as $event) {
                if ($event->getType() == Event::TYPE_GOAL) {
                    $report->awayScore += 1;
                }
            }
        }

        return $report;
    }

    /**
     * Process shoots
     *
     * @param Team $attackingTeam
     * @param Team $defendingTeam
     * @param int $count
     * @return Event[]
     */
    private function processShoots(Team $attackingTeam, Team $defendingTeam, int $count): array
    {
        $events = [];

        $saveBonus = 0;
        for ($i = $count; $i > 0; $i--) {
            $config = $this->buildShootConfig($attackingTeam, $defendingTeam);
            $config->saveBonus = $saveBonus;
            $shootResult = $this->shootEngine->shoot($config);

            if ($shootResult->isGoal()) {
                $saveBonus = ($saveBonus <= -2) ? 0 : $saveBonus + 1;
            } else {
                $saveBonus = ($saveBonus >= 2) ? 0 : $saveBonus - 1;
            }

            $events[] = Event::fromShootResult($shootResult);
        }

        return $events;
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

        $minute = rand(1, 93);

        $striker = LineupHelper::getRandomPlayerInPosition($attackingTeam->getLineup(), $pos, $minute);
        $goalkeeper = LineupHelper::getRandomPlayerInPosition($defendingTeam->getLineup(), Player::POS_G, $minute);

        $config = new ShootConfig();

        $config->minute = $minute;
        $config->attackingTeamId = $attackingTeam->getId();
        $config->defendingTeamId = $defendingTeam->getId();
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
     * @param MatchReport $report
     */
    private function addAttackStopEvents(int $count, Team $attackingTeam, Team $defendingTeam, MatchReport $report)
    {
        for ($i = $count; $i > 0; $i--) {
            $minute = rand(1, 93);

            $data = [
                'attacking_team' => $attackingTeam->getId(),
                'defending_team' => $defendingTeam->getId(),
            ];

            $event = new Event(Event::TYPE_TACKLE, $minute, $data);
            $report->addEvent($event);
        }
    }

    /**
     * Modify team strengths based on multiple factors
     *
     * @param Team $team
     * @param MatchSettings $settings
     */
    private function modifyStrengths(Team $team, MatchSettings $settings)
    {
        // modify by coach
        $this->modifyStrengthByCoach($team, $settings);

        $team->getStrength()->applyTactic($team->getTactic());
    }

    private function applyHomeTeamBonus(SquadStrength $strength, MatchSettings $settings)
    {
        $modifier = new RelativeSquadStrengthModifier();
        $modifier->defenseModifier = $settings->homeTeamBonus;
        $modifier->midfieldModifier = $settings->homeTeamBonus;
        $modifier->attackModifier = $settings->homeTeamBonus;
        $strength->applyModifier($modifier);
    }

    /**
     * Modify team strength based on coach
     *
     * @param Team $team
     * @param MatchSettings $settings
     */
    public function modifyStrengthByCoach(Team $team, MatchSettings $settings)
    {
        $coach = $team->getCoach();
        if (!$coach) {
            return;
        }
        $levelBonus = $settings->coachLevelBonus * $coach->getLevel();

        $defenseModifier = $levelBonus;
        $midfieldModifier = $levelBonus;
        $attackModifier = $levelBonus;

        if ($coach->getSpeciality() == Coach::SPECIALITY_DEF) {
            $defenseModifier *= $settings->coachSpecialityBonus;
        } elseif ($coach->getSpeciality() == Coach::SPECIALITY_MID) {
            $midfieldModifier *= $settings->coachSpecialityBonus;
        } elseif ($coach->getSpeciality() == Coach::SPECIALITY_ATT) {
            $attackModifier *= $settings->coachSpecialityBonus;
        }

        $modifier = new FlatSquadStrengthModifier();
        $modifier->defenseModifier = $defenseModifier;
        $modifier->midfieldModifier = $midfieldModifier;
        $modifier->attackModifier = $attackModifier;

        $team->getStrength()->applyModifier($modifier);
    }
}