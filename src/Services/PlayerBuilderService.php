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
            'id' => uniqid(),
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
     * @return Player
     * @throws EngineException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function buildRandomPlayer(string $position): Player
    {
        $params = getContainer()->get(PlayerBuilderRandomParams::class);

        $params->position = $position;

        return $this->buildPlayer($params);
    }
}