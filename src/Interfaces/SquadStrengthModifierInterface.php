<?php

namespace rkistaps\Engine\Interfaces;

use rkistaps\Engine\Structures\SquadStrength;

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