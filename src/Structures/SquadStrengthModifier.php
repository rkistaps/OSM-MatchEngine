<?php

namespace rkistaps\Engine\Structures;

class SquadStrengthModifier
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
     * Creates empty modifier
     *
     * @return SquadStrengthModifier
     */
    public static function getEmpty(): SquadStrengthModifier
    {
        $modifier = new SquadStrengthModifier();
        $modifier->goalkeeperModifier = 0;
        $modifier->defenseModifier = 0;
        $modifier->midfieldModifier = 0;
        $modifier->attackModifier = 0;

        return $modifier;
    }
}