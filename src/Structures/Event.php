<?php

namespace rkistaps\Engine\Structures;

class Event
{
    const TYPE_GOAL = 'goal';
    const TYPE_SAVE = 'save';
    const TYPE_TACKLE = 'tackle';

    /** @var integer */
    public $minute;

    /** @var string */
    public $type;
}