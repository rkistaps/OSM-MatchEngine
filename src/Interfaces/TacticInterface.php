<?php

namespace rkistaps\Engine\Interfaces;

use rkistaps\Engine\Structures\SquadStrength;
use rkistaps\Engine\Structures\SquadStrengthModifier;

interface TacticInterface
{
    /**
     * Get SquadStrengthModifier for tactic
     *
     * @param SquadStrength $strength
     * @return SquadStrengthModifier
     */
    public function getSquadStrengthModifier(SquadStrength $strength): SquadStrengthModifier;
}