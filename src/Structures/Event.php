<?php

namespace OSM\MatchEngine\Structures;

class Event
{
    const TYPE_GOAL = 'goal';
    const TYPE_SAVE = 'save';
    const TYPE_TACKLE = 'tackle';
    const TYPE_YELLOW_CARD = 'yellow_card';
    const TYPE_RED_CARD = 'red_card';
    const TYPE_SECOND_YELLOW_CARD = 'second_yellow_card';

    /** @var integer */
    private $minute;

    /** @var string */
    private $type;

    /** @var array */
    private $data = [];

    /**
     * Event constructor.
     *
     * @param string $type
     * @param int $minute
     * @param array $data
     */
    public function __construct(string $type, int $minute, array $data = [])
    {
        $this->type = $type;
        $this->minute = $minute;
        $this->data = $data;
    }

    /**
     * Get event type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get event minute
     *
     * @return int
     */
    public function getMinute(): int
    {
        return $this->minute;
    }

    /**
     * Build Event from ShootResult
     *
     * @param ShootResult $result
     * @return Event
     */
    public static function fromShootResult(ShootResult $result): Event
    {
        $shootConfig = $result->getShootConfig();

        $data = [
            'striker' => $shootConfig->striker->getId(),
            'goalkeeper' => $shootConfig->goalkeeper->getId(),
            'attackHelper' => $shootConfig->attackHelper ? $shootConfig->attackHelper->getId() : null,
            'defenseHelper' => $shootConfig->defenseHelper ? $shootConfig->defenseHelper->getId() : null,
            'attackingTeamId' => $shootConfig->attackingTeamId,
            'defendingTeamId' => $shootConfig->defendingTeamId,
        ];

        $type = $result->isGoal() ? self::TYPE_GOAL : self::TYPE_SAVE;
        $event = new Event($type, $shootConfig->minute, $data);

        return $event;
    }
}