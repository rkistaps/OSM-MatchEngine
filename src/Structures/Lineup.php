<?php

namespace rkistaps\Engine\Structures;

use rkistaps\Engine\Exceptions\EngineException;

class Lineup
{
    /** @var Player[] */
    private $players = [];

    /**
     * Add player to lineup
     *
     * @param Player $player
     */
    public function addPlayer(Player $player)
    {
        $this->players[] = $player;
    }

    /**
     * Get players in position
     *
     * @param string $position
     * @return Player[]
     */
    public function getPlayersInPosition(string $position): array
    {
        $list = [];
        foreach ($this->players as $player) {
            if ($player->getPosition() == $position) {
                $list[] = $player;
            }
        }

        return $list;
    }

    /**
     * Get position strength
     *
     * @param string $pos
     * @return int
     */
    public function getPositionStrength(string $pos): int
    {
        $strength = 0;

        foreach ($this->players as $player) {
            if ($player->getPosition() === $pos) {
                $strength += $player->getPerformance();
            }
        }

        return floor($strength);
    }

    /**
     * Process perform on each lineup player
     */
    public function perform()
    {
        foreach ($this->players as $player) {
            $player->perform();
        }
    }

    /**
     * Build lineup from array
     *
     * @param array $array
     * @return Lineup
     * @throws EngineException
     */
    public static function fromArray(array $array): Lineup
    {
        $lineup = new Lineup;

        foreach ($array as $item) {
            $player = Player::fromArray($item);
            $lineup->addPlayer($player);
        }

        return $lineup;
    }
}