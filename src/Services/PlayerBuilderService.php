<?php

namespace OSM\Services;

use OSM\Exceptions\EngineException;
use OSM\Interfaces\PlayerBuilderParamInterface;
use OSM\Structures\Params\PlayerBuilderRandomParams;
use OSM\Structures\Player;
use OSM\Structures\PlayerAttributes;

class PlayerBuilderService
{
    /**
     * Builds Player
     *
     * @param PlayerBuilderParamInterface $params
     * @return Player
     * @throws EngineException
     */
    public function buildPlayer(PlayerBuilderParamInterface $params): Player
    {
        $attributes = PlayerAttributes::fromArray([
            'id' => $params->getId(),
            'position' => $params->getPosition(),
            'skill' => $params->getSkill(),
            'energy' => $params->getEnergy()
        ]);

        return new Player($attributes);
    }

    /**
     * Builds random player for position
     *
     * @param string $position
     * @param int $minSkill
     * @param int $maxSkill
     * @return Player
     * @throws EngineException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function buildRandomPlayer(string $position, int $minSkill = 100, int $maxSkill = 200): Player
    {
        $params = getContainer()->get(PlayerBuilderRandomParams::class);
        $params->minSkill = $minSkill;
        $params->maxSkill = $maxSkill;

        $params->position = $position;

        return $this->buildPlayer($params);
    }
}