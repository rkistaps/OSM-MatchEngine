<?php

namespace OSM\Interfaces;

use OSM\Structures\SquadStrength;

interface TacticInterface
{
    /**
     * Apply tactic to squad strength
     *
     * @param SquadStrength $strength
     */
    public function apply(SquadStrength $strength);
}