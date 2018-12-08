<?php

namespace rkistaps\Engine\Structures;

use rkistaps\Engine\Exceptions\EngineException;
use rkistaps\Engine\Helpers\ArrayHelper;

class PlayerAttributes
{
    /** @var int */
    public $id = 0;

    /** @var string */
    public $position;

    /** @var int */
    public $skill = 0;

    /** @var int */
    public $energy = 0;

    /**
     * Build attributes from array
     *
     * @param array $array
     * @return PlayerAttributes
     * @throws EngineException
     */
    public static function fromArray(array $array): PlayerAttributes
    {
        $id = ArrayHelper::get('id', $array);
        if (!$id) {
            throw new EngineException("Can't build player from array: missing id");
        }

        $position = ArrayHelper::get('position', $array);
        if (!$position) {
            throw new EngineException("Can't build player from array: missing position");
        }

        $skill = ArrayHelper::get('skill', $array);
        if (!$skill) {
            throw new EngineException("Can't build player from array: missing skill");
        }

        $energy = ArrayHelper::get('energy', $array);
        if (!$energy) {
            throw new EngineException("Can't build player from array: missing energy");
        }

        $attributes = new PlayerAttributes();
        $attributes->id = $id;
        $attributes->skill = $skill;
        $attributes->position = $position;
        $attributes->energy = $energy;

        return $attributes;
    }

}