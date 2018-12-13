<?php

namespace OSM\Structures;

use OSM\Exceptions\EngineException;
use OSM\Helpers\ArrayHelper;

class PlayerAttributes
{
    /** @var int|string */
    public $id = 0;

    /** @var string */
    public $position;

    /** @var int */
    public $skill = 0;

    /** @var int */
    public $energy = 100;

    /**
     * Build attributes from array
     *
     * @param array $array
     * @return PlayerAttributes
     * @throws EngineException
     */
    public static function fromArray(array $array): PlayerAttributes
    {
        $attributes = new PlayerAttributes();

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
        if (!is_null($energy)) {
            $attributes->energy = (int)$energy;
        }

        $attributes->id = $id;
        $attributes->skill = $skill;
        $attributes->position = $position;

        return $attributes;
    }

}