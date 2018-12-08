<?php

namespace rkistaps\Engine\Structures;

use rkistaps\Engine\Interfaces\TacticInterface;

class SquadStrength
{
    /** @var int */
    private $goalkeeper = 0;

    /** @var float */
    private $defence = 0;

    /** @var float */
    private $midfield = 0;

    /** @var float */
    private $attack = 0;

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
     * @return float
     */
    public function getGoalkeeper(): float
    {
        return $this->goalkeeper;
    }

    /**
     * @return float
     */
    public function getDefense(): float
    {
        return $this->defence;
    }

    /**
     * @return float
     */
    public function getMidfield(): float
    {
        return $this->midfield;
    }

    /**
     * @return float
     */
    public function getAttack(): float
    {
        return $this->attack;
    }

    /**
     * Return strength
     *
     * @return array
     */
    public function get(): array
    {
        return [
            $this->goalkeeper,
            $this->defence,
            $this->midfield,
            $this->attack,
        ];
    }

    /**
     * Modify strength by multiplying modifier values
     *
     * @param SquadStrengthModifier $modifier
     */
    public function modify(SquadStrengthModifier $modifier)
    {
        $this->goalkeeper = $this->goalkeeper * $modifier->goalkeeperModifier;
        $this->defence = $this->defence * $modifier->defenseModifier;
        $this->midfield = $this->midfield * $modifier->midfieldModifier;
        $this->attack = $this->attack * $modifier->attackModifier;
    }

    /**
     * Modify strength by adding modifier values
     *
     * @param SquadStrengthModifier $modifier
     */
    public function modifyFlat(SquadStrengthModifier $modifier)
    {
        $this->goalkeeper = $this->goalkeeper + $modifier->goalkeeperModifier;
        $this->defence = $this->defence + $modifier->defenseModifier;
        $this->midfield = $this->midfield + $modifier->midfieldModifier;
        $this->attack = $this->attack + $modifier->attackModifier;
    }

    /**
     * Apply tactic
     *
     * @param TacticInterface $tactic
     */
    public function applyTactic(TacticInterface $tactic)
    {
        $modifier = $tactic->getSquadStrengthModifier($this);
        $this->modifyFlat($modifier);
    }
}