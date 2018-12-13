<?php

namespace OSM\MatchEngine\Structures;

class ShootResult
{
    const RESULT_GOAL = 'goal';
    const RESULT_SAVE = 'save';

    /** @var string */
    private $result;

    /** @var ShootConfig */
    private $shootConfig;

    /**
     * ShootResult constructor.
     * @param string $result
     * @param ShootConfig $config
     */
    public function __construct(string $result, ShootConfig $config)
    {
        $this->result = $result;
        $this->shootConfig = $config;
    }

    /**
     * @return ShootConfig
     */
    public function getShootConfig(): ShootConfig
    {
        return $this->shootConfig;
    }

    /**
     * @return string
     */
    public function getResult(): string
    {
        return $this->result;
    }

    /**
     * Returns if result is a goal
     *
     * @return bool
     */
    public function isGoal(): bool
    {
        return $this->result == ShootResult::RESULT_GOAL;
    }
}