<?php

namespace OSM\Interfaces;

use OSM\Structures\SquadStrength;

interface SquadStrengthModifierInterface
{
    /**
     * Apply modifier to squad strength
     *
     * @param SquadStrength $strength
     * @return mixed
     */
    public function apply(SquadStrength $strength);
}