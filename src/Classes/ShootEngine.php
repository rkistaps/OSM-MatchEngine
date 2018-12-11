<?php

namespace rkistaps\Engine\Classes;

use rkistaps\Engine\Structures\ShootConfig;
use rkistaps\Engine\Structures\ShootResult;

class ShootEngine
{
    /**
     * Calculate shoot result
     *
     * @param ShootConfig $shootConfig
     * @return ShootResult
     */
    public function shoot(ShootConfig $shootConfig): ShootResult
    {
        $strikerPref = $shootConfig->striker->getPerformance();
        $goalkeeperPref = $shootConfig->goalkeeper->getPerformance();

        $attackHelperPref = $shootConfig->attackHelper ? $shootConfig->attackHelper->getPerformance() : 0;
        $defenseHelperPref = $shootConfig->defenseHelper ? $shootConfig->defenseHelper->getPerformance() : 0;

        $goalK = round($strikerPref * $strikerPref / ($strikerPref * $strikerPref + $goalkeeperPref * $goalkeeperPref), 2);

        $helperBonus = 0;
        if ($shootConfig->attackHelper && $shootConfig->defenseHelper) { // both helpers
            $helperBonus = round(($attackHelperPref / ($attackHelperPref + $defenseHelperPref) - 0.5), 2);
        } elseif ($shootConfig->attackHelper && !$shootConfig->defenseHelper) {
            $helperBonus = round($attackHelperPref / ($goalkeeperPref * 2.5), 2);
        } elseif (!$shootConfig->attackHelper && $shootConfig->defenseHelper) {
            $helperBonus = round($defenseHelperPref / ($strikerPref * 2.5), 2);
        }

        $goalK += $helperBonus;
        $saveK = round(0.5 + rand(-7, 7) / 100, 2);

        $saveBonusK = $shootConfig->saveBonus * 0.05;

        $goal = $goalK - $saveBonusK > $saveK;

        $resultType = $goal ? ShootResult::RESULT_GOAL : ShootResult::RESULT_SAVE;
        $result = new ShootResult($resultType, $shootConfig);

        return $result;
    }
}