<?php

namespace rkistaps\Engine\Structures;

class MatchSettings
{
    /** @var float */
    public $homeTeamBonus = 1.15;

    /** @var bool */
    public $hasHomeTeamBonus = true;

    /** @var float */
    public $coachSpecialityBonus = 1.15;

    /** @var float */
    public $defenseModifier = 1;

    /** @var float */
    public $performanceRandomRange = 0.2;
}