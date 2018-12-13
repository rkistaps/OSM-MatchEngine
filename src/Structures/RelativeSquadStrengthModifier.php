<?php

namespace OSM\Structures;

use OSM\Interfaces\SquadStrengthModifierInterface;

class RelativeSquadStrengthModifier implements SquadStrengthModifierInterface
{
    /** @var float */
    public $goalkeeperModifier = 1;
    /** @var float */
    public $defenseModifier = 1;
    /** @var float */
    public $midfieldModifier = 1;
    /** @var float */
    public $attackModifier = 1;

    /**
     * Apply modifier to strength
     *
     * @param SquadStrength $strength
     */
    public function apply(SquadStrength $strength)
    {
        $strength->goalkeeper *= $this->goalkeeperModifier;
        $strength->defence *= $this->defenseModifier;
        $strength->midfield *= $this->midfieldModifier;
        $strength->attack *= $this->attackModifier;
    }
}