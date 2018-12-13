<?php

namespace OSM\MatchEngine\Structures\Tactics;

use OSM\MatchEngine\Interfaces\TacticInterface;
use OSM\MatchEngine\Structures\FlatSquadStrengthModifier;
use OSM\MatchEngine\Structures\SquadStrength;

class PlayItWideTactic implements TacticInterface
{
    // 20% of mid goes 10% to attack and 10% to defense
    const MODIFIER = 20;

    /**
     * Apply tactic to squad strength
     *
     * @param SquadStrength $strength
     */
    public function apply(SquadStrength $strength)
    {
        $mid = floor($strength->midfield * PlayItWideTactic::MODIFIER / 100);

        $modifier = new FlatSquadStrengthModifier();
        $modifier->midfieldModifier = $mid * (-1);
        $modifier->defenseModifier = floor($mid / 2);
        $modifier->attackModifier = floor($mid / 2);

        $strength->applyModifier($modifier);
    }
}