<?php

namespace OSM\MatchEngine\Helpers;

use OSM\MatchEngine\Structures\Lineup;
use OSM\MatchEngine\Structures\Player;

class LineupHelper
{
    /**
     * @param Lineup $lineup
     * @param string $position
     * @param int $minute
     * @return Player|null
     */
    public static function getRandomPlayerInPosition(Lineup $lineup, string $position, int $minute)
    {
        $players = $lineup->getPlayersInPosition($position);

        $players = array_filter($players, function (Player $player) use ($position) {
            return $player->getPosition() == $position;
        });

        $players = array_filter($players, function (Player $player) use ($minute) {
            return $player->minuteFrom <= $minute && $player->minuteTo >= $minute;
        });

        return $players ? $players[rand(0, count($players) - 1)] : null;
    }
}