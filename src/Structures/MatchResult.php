<?php

namespace rkistaps\Engine\Structures;

class MatchResult
{
    /** @var int */
    public $homeScore = 0;

    /** @var int */
    public $awayScore = 0;

    /** @var Event[] */
    private $events = [];

    /**
     * Add event to list
     *
     * @param Event $event
     */
    public function addEvent(Event $event)
    {
        $this->events[] = $event;
    }

    /**
     * Return event list
     *
     * @return Event[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }
}