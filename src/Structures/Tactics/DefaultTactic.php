<?php

namespace rkistaps\Engine\Structures\Tactics;

use rkistaps\Engine\Interfaces\TacticInterface;
use rkistaps\Engine\Structures\SquadStrength;
use rkistaps\Engine\Structures\SquadStrengthModifier;

class DefaultTactic implements TacticInterface
{
    /**
     * Get SquadStrengthModifier for tactic
     *
     * @param SquadStrength $strength
     * @return SquadStrengthModifier
     */
    public function getSquadStrengthModifier(SquadStrength $strength): SquadStrengthModifier
    {
        $modifier = SquadStrengthModifier::getEmpty();

        return $modifier;
    }
}