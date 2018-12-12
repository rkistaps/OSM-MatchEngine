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
        $homeTeamMidStrength = $homeTeamStrength->getMidfield();
        $awayTeamMidStrength = $awayTeamStrength->getMidfield();

        $homeTeamK = $homeTeamMidStrength * 2 / $awayTeamMidStrength;
        $awayTeamK = $awayTeamMidStrength * 2 / $homeTeamMidStrength;

        $randomModifier = rand(100 - $this->settings->randomRange, 100 + $this->settings->randomRange) / 100;

        $homeTeamPossession = $homeTeamK / ($homeTeamK + $awayTeamK) * $randomModifier;
        $homeTeamPossession = $homeTeamPossession < 0.99 ? $homeTeamPossession : 0.99;

        $awayTeamPossession = 1 - $homeTeamPossession;

        $possession = new Possession();
        $possession->homeTeam = $homeTeamPossession;
        $possession->awayTeam = $awayTeamPossession;

        return $possession;
    }
}