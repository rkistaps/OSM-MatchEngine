<?php

namespace OSM\MatchEngine\Structures\Tactics;

use OSM\MatchEngine\Interfaces\TacticInterface;
use OSM\MatchEngine\Structures\SquadStrength;

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