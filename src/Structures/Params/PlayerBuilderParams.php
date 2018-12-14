<?php

namespace OSM\Structures\Params;

use OSM\Interfaces\PlayerBuilderParamInterface;

class PlayerBuilderRandomParams implements PlayerBuilderParamInterface
{
    /** @var string */
    public $position = '';

    /** @var int */
    public $minSkill = 0;

    /** @var int */
    public $maxSkill = 0;

    /** @var int */
    public $minAge = 0;

    /** @var int */
    public $maxAge = 0;

    /**
     * @return int
     */
    public function getAge(): int
    {
        return rand($this->minAge, $this->maxAge);
    }

    /**
     * @return int
     */
    public function getSkill(): int
    {
        return rand($this->minSkill, $this->maxSkill);
    }

    /**
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
    }
}