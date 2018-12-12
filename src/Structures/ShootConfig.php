<?php

namespace rkistaps\Engine\Structures;

class ShootConfig
{
    /** @var int */
    public $minute = 0;

    /** @var string */
    public $attackingTeamId;

    /** @var string */
    public $defendingTeamId;

    /** @var Player */
    public $striker;

    /** @var Player */
    public $goalkeeper;

    /** @var Player|null */
    public $attackHelper;

    /** @var Player|null */
    public $defenseHelper;

    /** @var int */
    public $saveBonus = 0;

    /** @var int */
    public $randomModifier = 7;
}