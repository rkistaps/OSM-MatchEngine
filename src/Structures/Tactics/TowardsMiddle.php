<?php

namespace rkistaps\Engine\Structures\Tactics;

use rkistaps\Engine\Interfaces\TacticInterface;
use rkistaps\Engine\Structures\SquadStrength;
use rkistaps\Engine\Structures\SquadStrengthModifier;

class TowardsMiddle implements TacticInterface
{
    // 10% of attack and defense goes to mid
    const MODIFIER = 10;

    /**
     * Apply tactic to squad strength
     *
     * @param SquadStrength $strength
     */
    public function apply(SquadStrength $strength)
    {
        $modifier = new SquadStrengthModifier();

        $attack = $strength->getAttack();
        $defense = $strength->getDefense();

        $attackModifier = floor($attack * TowardsMiddle::MODIFIER / 100);
        $defenseModifier = floor($defense * TowardsMiddle::MODIFIER / 100);
        $midfieldModifier = $attackModifier + $defenseModifier;

        $modifier->defenseModifier = $defenseModifier * (-1);
        $modifier->midfieldModifier = $midfieldModifier;
        $modifier->attackModifier = $attackModifier * (-1);

        $strength->modifyFlat($modifier);
    }
}