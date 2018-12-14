<?php

namespace OSM\Structures;

use OSM\Exceptions\EngineException;
use OSM\Services\ArrayHelper;

class Player
{
    const POS_G = 'goalkeeper';
    const POS_D = 'defender';
    const POS_M = 'midfielder';
    const POS_F = 'forward';

    const POSITIONS = [
        Player::POS_G,
        Player::POS_D,
        Player::POS_M,
        Player::POS_F,
    ];

    /** @var PlayerAttributes */
    private $attributes;

    /** @var int */
    private $performance = 0;

    /** @var float */
    private $performanceK = 1;

    /** @var int */
    public $minuteFrom = 1;

    /** @var int */
    public $minuteTo = 93;

    // stats
    public $goals = 0;
    public $yellowCards = 0;
    public $redCard = false;
    public $shotsOnGoal = 0;
    public $assists = 0;
    public $tackles = 0;

    /**
     * Player constructor.
     *
     * @param PlayerAttributes $attributes
     */
    public function __construct(PlayerAttributes $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Get Player id
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->attributes->id;
    }

    /**
     * Get players position
     *
     * @return string
     */
    public function getPosition(): string
    {
        return $this->attributes->position;
    }

    /**
     * Get players performance
     *
     * @return float
     */
    public function getPerformance(): float
    {
        return $this->performance;
    }

    /**
     * Perform
     * @param int $performanceRandomRange
     */
    public function perform(int $performanceRandomRange)
    {
        $this->performanceK = rand(100 - $performanceRandomRange, 100 + $performanceRandomRange) / 100;
        $this->performance = floor($this->attributes->skill * $this->performanceK * $this->attributes->energy / 100);
    }

    /**
     * Builds player from array
     *
     * @param array $array
     * @return Player
     * @throws EngineException
     */
    public static function fromArray(array $array): Player
    {
        $attrArray = ArrayHelper::get('attributes', $array);
        if (!$attrArray) {
            throw new EngineException('No player attributes');
        }
        $attributes = PlayerAttributes::fromArray($attrArray);

        return new Player($attributes);
    }
}
