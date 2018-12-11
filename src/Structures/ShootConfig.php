<?php

namespace rkistaps\Engine\Structures;

class ShootConfig
{
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
}