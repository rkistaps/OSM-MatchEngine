<?php

namespace OSM\Structures;

use OSM\Exceptions\EngineException;
use OSM\Interfaces\TacticInterface;
use OSM\Structures\Tactics\DefaultTactic;
use OSM\Structures\Tactics\PlayItWideTactic;
use OSM\Structures\Tactics\TowardsMiddle;

class TacticRepository
{
    const Tactics = [
        'default' => DefaultTactic::class,
        'play_it_wide' => PlayItWideTactic::class,
        'towards_middle' => TowardsMiddle::class,
    ];

    /**
     * Get tactic class by name
     *
     * @param string $name
     * @return TacticInterface
     * @throws EngineException
     */
    public static function getTactic(string $name): TacticInterface
    {
        if (!isset(TacticRepository::Tactics[$name])) {
            throw new EngineException('Unknown tactic name');
        }

        $className = TacticRepository::Tactics[$name];

        return new $className;
    }
}