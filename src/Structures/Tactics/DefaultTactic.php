<?php

namespace OSM\Structures\Tactics;

use OSM\Interfaces\TacticInterface;
use OSM\Structures\SquadStrength;

class DefaultTactic implements TacticInterface
{
    /**
     * Apply tactic to squad strength
     *
     * @param SquadStrength $strength
     */
    public function apply(SquadStrength $strength)
    {

    }
}