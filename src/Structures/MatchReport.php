<?php

namespace rkistaps\Engine\Structures;

class MatchReport
{
    /** @var int */
    public $homeScore = 0;

    /** @var int */
    public $homeTeamAttackCount = 0;

    /** @var int */
    public $homeTeamShootCount = 0;

    /** @var int */
    public $awayScore = 0;

    /** @var int */
    public $awayTeamAttackCount = 0;

    /** @var int */
    public $awayTeamShootCount = 0;

    /** @var Possession|null */
    public $possession;

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
     * Add array of Events
     *
     * @param Event[] $events
     */
    public function addEvents(array $events)
    {
        foreach ($events as $event) {
            $this->addEvent($event);
        }
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