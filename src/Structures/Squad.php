<?php

namespace rkistaps\Engine\Structures;

use rkistaps\Engine\Classes\LineupValidator;
use rkistaps\Engine\Exceptions\EngineException;
use rkistaps\Engine\Helpers\ArrayHelper;

class Squad
{
    /** @var Lineup */
    private $lineup;

    /** @var Coach|null */
    private $coach;

    /** @var SquadStrength */
    private $strength;

    /**
     * Set squad lineup
     *
     * @param Lineup $lineup
     * @throws EngineException
     */
    public function __construct(Lineup $lineup)
    {
        if (!LineupValidator::staticValidate($lineup)) {
            throw new EngineException("Invalid lineup");
        }

        $this->lineup = $lineup;
    }

    /**
     * Set squad coach
     *
     * @param Coach $coach
     */
    public function setCoach(Coach $coach)
    {
        $this->coach = $coach;
    }

    /**
     * Get squad strength
     *
     * @return SquadStrength
     */
    public function calculateStrength(): SquadStrength
    {
        $strength = new SquadStrength();

        $strength->defence = $this->lineup->getPositionStrength(Player::POS_D);
        $strength->midfield = $this->lineup->getPositionStrength(Player::POS_M);
        $strength->attack = $this->lineup->getPositionStrength(Player::POS_F);

        return $strength;
    }

    /**
     * @return SquadStrength|null
     */
    public function getStrength()
    {
        return $this->strength;
    }

    /**
     * Preform
     */
    public function perform()
    {
        $this->lineup->perform();
        $this->strength = $this->calculateStrength();
    }

    /**
     * Get squad lineup
     *
     * @return Lineup
     */
    public function getLineup(): Lineup
    {
        return $this->lineup;
    }

    /**
     * Build squad from array
     *
     * @param array $array
     * @return Squad
     * @throws EngineException
     */
    public static function fromArray(array $array): Squad
    {
        $lineupArr = ArrayHelper::get('lineup', $array);
        if (!$lineupArr) {
            throw new EngineException('Missing lineup');
        }

        $lineup = Lineup::fromArray($lineupArr);

        $squad = new Squad($lineup);


        return $squad;
    }
}