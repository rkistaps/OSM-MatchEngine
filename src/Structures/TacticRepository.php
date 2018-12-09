<?php

namespace rkistaps\Engine\Structures;

use rkistaps\Engine\Exceptions\EngineException;
use rkistaps\Engine\Interfaces\TacticInterface;
use rkistaps\Engine\Structures\Tactics\DefaultTactic;

class TacticRepository
{
    const Tactics = [
        'default' => DefaultTactic::class,
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