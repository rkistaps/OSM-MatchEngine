<?php

namespace rkistaps\Engine\Structures\Tactics;

use rkistaps\Engine\Interfaces\TacticInterface;
use rkistaps\Engine\Structures\SquadStrength;
use rkistaps\Engine\Structures\SquadStrengthModifier;

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
        $mid = floor($strength->getMidfield() * PlayItWideTactic::MODIFIER / 100);

        $modifier = new SquadStrengthModifier();
        $modifier->midfieldModifier = $mid * (-1);
        $modifier->defenseModifier = floor($mid / 2);
        $modifier->attackModifier = floor($mid / 2);

        $strength->modifyFlat($modifier);
    }
}