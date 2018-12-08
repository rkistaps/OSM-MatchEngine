<?php

namespace rkistaps\Engine\Classes;

use rkistaps\Engine\Structures\Possession;
use rkistaps\Engine\Structures\Settings\PossessionCalculatorSettings;
use rkistaps\Engine\Structures\SquadStrength;

class PossessionCalculator
{
    /** @var PossessionCalculatorSettings */
    private $settings;

    /**
     * PossessionCalculator constructor.
     *
     * @param PossessionCalculatorSettings $settings
     */
    public function __construct(PossessionCalculatorSettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Calculate ball possession
     *
     * @param SquadStrength $homeTeamStrength
     * @param SquadStrength $awayTeamStrength
     * @return Possession
     */
    public function calculate(SquadStrength $homeTeamStrength, SquadStrength $awayTeamStrength): Possession
    {
        $homeTeamStrength = $homeTeamStrength->getMidfield();
        $awayTeamStrength = $awayTeamStrength->getMidfield();

        $homeTeamK = $homeTeamStrength * 2 / $awayTeamStrength;
        $awayTeamK = $awayTeamStrength * 2 / $homeTeamStrength;

        $randomModifier = rand(1 - $this->settings->randomRange, 1 + $this->settings->randomRange);

        $homeTeamPossession = $homeTeamK / ($homeTeamK + $awayTeamK) * $randomModifier;
        $homeTeamPossession = $homeTeamPossession < 0.99 ?: 0.99;

        $awayTeamPossession = 1 - $homeTeamPossession;

        $possession = new Possession();
        $possession->homeTeam = $homeTeamPossession;
        $possession->awayTeam = $awayTeamPossession;

        return $possession;
    }
}