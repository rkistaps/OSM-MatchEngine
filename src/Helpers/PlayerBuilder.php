<?php

namespace OSM\Helpers;

use OSM\Exceptions\EngineException;
use OSM\Structures\Player;
use OSM\Structures\PlayerAttributes;

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
    public static function buildRandomPlayer(string $position, int $minSkill = 100, int $maxSkill = 100, int $energy = 100): Player
    {
        $attributes = PlayerAttributes::fromArray([
            'id' => uniqid(),
            'position' => $position,
            'skill' => rand($minSkill, $maxSkill),
            'energy' => $energy
        ]);

//        var_dump($attributes);

        return new Player($attributes);
    }
}