<?php

namespace OSM\Structures;

use OSM\Exceptions\EngineException;
use OSM\Services\ArrayHelper;

class Coach
{
    const SPECIALITY_NON = 'none';
    const SPECIALITY_DEF = 'defence';
    const SPECIALITY_MID = 'midfield';
    const SPECIALITY_ATT = 'attack';

    const SPECIALITIES = [
        Coach::SPECIALITY_NON,
        Coach::SPECIALITY_DEF,
        Coach::SPECIALITY_MID,
        Coach::SPECIALITY_ATT,
    ];

    /** @var string */
    private $speciality;

    /** @var int */
    private $level = 1;

    /**
     * Coach constructor.
     *
     * @param string $speciality
     * @param int $level
     * @throws EngineException
     */
    public function __construct(string $speciality, int $level)
    {
        if(!in_array($speciality, Coach::SPECIALITIES)){
            throw new EngineException('Incorrect coach speciality');
        }

        $this->speciality = $speciality;
        $this->level = $level;
    }

    /**
     * Get coach speciality
     *
     * @return string
     */
    public function getSpeciality(): string
    {
        return $this->speciality;
    }

    /**
     * Get coach level
     *
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * Build coach from array
     *
     * @param array $array
     * @return Coach
     * @throws EngineException
     */
    public static function fromArray(array $array): Coach
    {
        $speciality = ArrayHelper::get('speciality', $array);

        if(!$speciality){
            throw new EngineException('No speciality provided');
        }

        $level = (int)ArrayHelper::get('level', $array, 1);

        return new Coach($speciality, $level);
    }
}