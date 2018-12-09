<?php

namespace rkistaps\Engine\Structures;

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
    public function __construct(string $type, int $minute, array $data = []) {
        $this->type = $type;
        $this->minute = $minute;
        $this->data = $data;
    }
}