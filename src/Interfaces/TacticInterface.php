<?php

namespace rkistaps\Engine\Interfaces;

use rkistaps\Engine\Structures\SquadStrength;

interface TacticInterface
{
    /**
     * Apply tactic to squad strength
     *
     * @param SquadStrength $strength
     */
    public function apply(SquadStrength $strength);
}