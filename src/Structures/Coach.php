<?php

namespace rkistaps\Engine\Structures;

use rkistaps\Engine\Exceptions\EngineException;
use rkistaps\Engine\Helpers\ArrayHelper;

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

    /**
     * @var string
     */
    private $speciality;

    /**
     * Coach constructor.
     *
     * @param string $speciality
     * @throws EngineException
     */
    public function __construct(string $speciality)
    {
        if(!in_array($speciality, Coach::SPECIALITIES)){
            throw new EngineException('Incorrect coach speciality');
        }

        $this->speciality = $speciality;
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

        return new Coach($speciality);
    }
}