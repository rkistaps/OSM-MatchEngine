<?php

namespace rkistaps\Engine\Structures;

use rkistaps\Engine\Classes\LineupValidator;
use rkistaps\Engine\Exceptions\EngineException;
use rkistaps\Engine\Helpers\ArrayHelper;
use rkistaps\Engine\Interfaces\TacticInterface;

class Team
{
    /** @var Lineup */
    private $lineup;

    /** @var TacticInterface */
    private $tactic;

    /** @var Coach|null */
    private $coach;

    /** @var SquadStrength */
    private $strength;

    /**
     * Set squad lineup
     *
     * @param Lineup $lineup
     * @param TacticInterface $tactic
     * @throws EngineException
     */
    public function __construct(Lineup $lineup, TacticInterface $tactic)
    {
        if (!LineupValidator::staticValidate($lineup)) {
            throw new EngineException("Invalid lineup");
        }

        $this->lineup = $lineup;
        $this->tactic = $tactic;
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
     * Return teams coach
     *
     * @return Coach|null
     */
    public function getCoach()
    {
        return $this->coach;
    }

    /**
     * Get team tactic
     *
     * @return TacticInterface
     */
    public function getTactic(): TacticInterface
    {
        return $this->tactic;
    }

    /**
     * Get squad strength
     *
     * @return SquadStrength
     */
    public function calculateStrength(): SquadStrength
    {
        $goalkeeper = $this->lineup->getPositionStrength(Player::POS_G);
        $defence = $this->lineup->getPositionStrength(Player::POS_D);
        $midfield = $this->lineup->getPositionStrength(Player::POS_M);
        $attack = $this->lineup->getPositionStrength(Player::POS_F);

        $strength = new SquadStrength($goalkeeper, $defence, $midfield, $attack);

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
     * @param float $performanceRandomRange
     */
    public function perform(float $performanceRandomRange)
    {
        $this->lineup->perform($performanceRandomRange);
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
     * @return Team
     * @throws EngineException
     */
    public static function fromArray(array $array): Team
    {
        $lineupArr = ArrayHelper::get('lineup', $array);
        if (!$lineupArr) {
            throw new EngineException('Missing lineup');
        }

        $tactic = ArrayHelper::get('tactic', $array);
        if (!$tactic) {
            throw new EngineException('Missing tactic');
        }

        $lineup = Lineup::fromArray($lineupArr);

        $squad = new Team($lineup, $tactic);

        return $squad;
    }
}