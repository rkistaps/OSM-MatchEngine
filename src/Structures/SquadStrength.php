<?php

namespace rkistaps\Engine\Structures;

use rkistaps\Engine\Interfaces\SquadStrengthModifierInterface;
use rkistaps\Engine\Interfaces\TacticInterface;

class SquadStrength
{
    /** @var int */
    public $goalkeeper = 0;

    /** @var float */
    public $defence = 0;

    /** @var float */
    public $midfield = 0;

    /** @var float */
    public $attack = 0;

    /**
     * SquadStrength constructor.
     *
     * @param float $goalkeeper
     * @param float $defence
     * @param float $midfield
     * @param float $attack
     */
    public function __construct(float $goalkeeper = 0, float $defence = 0, float $midfield = 0, float $attack = 0) {
        $this->goalkeeper = $goalkeeper;
        $this->defence = $defence;
        $this->midfield = $midfield;
        $this->attack = $attack;
    }

    /**
     * @param SquadStrengthModifierInterface $modifier
     */
    public function applyModifier(SquadStrengthModifierInterface $modifier)
    {
        $modifier->apply($this);
    }


    /**
     * Apply tactic
     *
     * @param TacticInterface $tactic
     */
    public function applyTactic(TacticInterface $tactic)
    {
        $tactic->apply($this);
    }
}