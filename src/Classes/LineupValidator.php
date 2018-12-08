<?php

namespace rkistaps\Engine\Classes;

use rkistaps\Engine\Structures\Lineup;
use rkistaps\Engine\Structures\Player;

class LineupValidator
{
    /** @var Lineup */
    private $lineup;

    const MIN_D = 3;
    const MAX_D = 5;

    const MIN_M = 3;
    const MAX_M = 5;

    const MIN_F = 1;
    const MAX_F = 3;

    /**
     * LineupValidator constructor.
     *
     * @param Lineup $lineup
     */
    public function __construct(Lineup $lineup)
    {
        $this->lineup = $lineup;
    }

    /**
     * Validate lineup
     *
     * @return bool
     */
    public function validate(): bool
    {
        $goalkeepers = count($this->lineup->getPlayersInPosition(Player::POS_G));
        $defenders = count($this->lineup->getPlayersInPosition(Player::POS_D));
        $midfielders = count($this->lineup->getPlayersInPosition(Player::POS_M));
        $forwards = count($this->lineup->getPlayersInPosition(Player::POS_F));

        if ($goalkeepers !== 1) {
            return false;
        }

        if ($defenders < LineupValidator::MIN_D || $defenders > LineupValidator::MAX_D) {
            return false;
        }

        if ($midfielders < LineupValidator::MIN_M || $midfielders > LineupValidator::MAX_M) {
            return false;
        }

        if ($forwards < LineupValidator::MIN_F || $forwards > LineupValidator::MAX_F) {
            return false;
        }

        return true;
    }

    /**
     * Static lineup validator
     *
     * @param Lineup $lineup
     * @return bool
     */
    public static function staticValidate(Lineup $lineup): bool
    {
        $validator = new LineupValidator($lineup);

        return $validator->validate();
    }
}
