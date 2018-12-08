<?php

namespace rkistaps\Engine\Structures;

class SquadStrength
{
    /** @var int */
    public $goalkeeper = 0;

    /** @var int */
    public $defence = 0;

    /** @var int */
    public $midfield = 0;

    /** @var int */
    public $attack = 0;

    /**
     * Apply bonus to all positions
     *
     * @param int $bonus
     */
    public function applyBonus(int $bonus)
    {
        $this->defence = round($this->defence * (1 + $bonus / 100));
        $this->midfield = round($this->midfield * (1 + $bonus / 100));
        $this->attack = round($this->attack * (1 + $bonus / 100));
    }

    /**
     * Apply bonus to all positions
     *
     * @param string $position
     * @param float $bonus
     */
    public function applyBonusToPosition(string $position, float $bonus)
    {
        if ($position == Player::POS_D) {
            $this->defence = round($this->defence * $bonus);
        } elseif ($position == Player::POS_M) {
            $this->midfield = round($this->midfield * $bonus);
        } elseif ($position == Player::POS_F) {
            $this->attack = round($this->attack * $bonus);
        }
    }
}