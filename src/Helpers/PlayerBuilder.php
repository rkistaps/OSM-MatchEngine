<?php

namespace rkistaps\Engine\Helpers;

use rkistaps\Engine\Exceptions\EngineException;
use rkistaps\Engine\Structures\Player;
use rkistaps\Engine\Structures\PlayerAttributes;

class PlayerBuilder
{
    /**
     * Builds random player for position
     *
     * @param string $position
     * @param int $minSkill
     * @param int $maxSkill
     * @param int $energy
     * @return Player
     * @throws EngineException
     */
    public static function buildRandomPlayer(string $position, int $minSkill = 60, int $maxSkill = 140, int $energy = 100): Player
    {
        $attributes = PlayerAttributes::fromArray([
            'id' => uniqid(),
            'position' => $position,
            'skill' => rand($minSkill, $maxSkill),
            'energy' => $energy
        ]);

        return new Player($attributes);
    }
}