<?php

namespace OSM\Structures;

use OSM\Interfaces\SquadStrengthModifierInterface;

class FlatSquadStrengthModifier implements SquadStrengthModifierInterface
{
    /** @var float */
    public $goalkeeperModifier = 0;
    /** @var float */
    public $defenseModifier = 0;
    /** @var float */
    public $midfieldModifier = 0;
    /** @var float */
    public $attackModifier = 0;

    /**
     * Apply modifier to strength
     *
     * @param SquadStrength $strength
     */
    public function apply(SquadStrength $strength)
    {
        $strength->goalkeeper += $this->goalkeeperModifier;
        $strength->defence += $this->defenseModifier;
        $strength->midfield += $this->midfieldModifier;
        $strength->attack += $this->attackModifier;
    }
}