<?php

namespace OSM\Services;

use OSM\Exceptions\EngineException;
use OSM\Structures\Lineup;
use OSM\Structures\Player;

class LineupBuilderService
{

    /** @var PlayerBuilderService */
    private $service;

    /**
     * LineupBuilderService constructor.
     * @param PlayerBuilderService $service
     */
    public function __construct(PlayerBuilderService $service) {
        $this->service = $service;
    }

    /**
     * Builds random lineup
     *
     * @param int $defenderCount
     * @param int $midfielderCount
     * @param int $forwardCount
     * @return Lineup
     * @throws EngineException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function buildRandomLineup(int $defenderCount = 4, int $midfielderCount = 4, int $forwardCount = 2): Lineup
    {
        if ($defenderCount + $midfielderCount + $forwardCount > 10) {
            throw new EngineException('Invalid player count');
        }

        $validDefenderCount = $defenderCount >= LineupValidator::MIN_D && $defenderCount <= LineupValidator::MAX_D;
        if (!$validDefenderCount) {
            throw new EngineException('Invalid defender count');
        }

        $validMidfielderCount = $midfielderCount >= LineupValidator::MIN_M && $midfielderCount <= LineupValidator::MAX_M;
        if (!$validMidfielderCount) {
            throw new EngineException('Invalid midfielder count');
        }

        $validForwardCount = $forwardCount >= LineupValidator::MIN_F && $forwardCount <= LineupValidator::MAX_F;
        if (!$validForwardCount) {
            throw new EngineException('Invalid forward count');
        }

        $lineup = new Lineup();

        $goalkeeper = $this->service->buildRandomPlayer(Player::POS_G);
        $lineup->addPlayer($goalkeeper);

        for ($i = 0; $i != $defenderCount; $i++) {
            $player = $this->service->buildRandomPlayer(Player::POS_D);
            $lineup->addPlayer($player);
        }

        for ($i = 0; $i != $midfielderCount; $i++) {
            $player = $this->service->buildRandomPlayer(Player::POS_M);
            $lineup->addPlayer($player);
        }

        for ($i = 0; $i != $forwardCount; $i++) {
            $player = $this->service->buildRandomPlayer(Player::POS_F);
            $lineup->addPlayer($player);
        }

        return $lineup;
    }
}