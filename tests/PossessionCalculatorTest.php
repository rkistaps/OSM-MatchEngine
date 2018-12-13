<?php

namespace Engine\Tests;

use OSM\Services\PossessionCalculator;
use OSM\Structures\Possession;
use OSM\Structures\Settings\PossessionCalculatorSettings;
use OSM\Structures\SquadStrength;

class PossessionCalculatorTest extends TestBase
{
    /**
     * Generic test
     */
    public function testGenericCalculator()
    {
        $posCalcSettings = new PossessionCalculatorSettings();
        $posCalcSettings->randomRange = 0; // no random in tests

        $str = 10;
        $homeTeamStr = $this->getSquadStrength($str);
        $awayTeamStr = $this->getSquadStrength($str);

        $calculator = new PossessionCalculator($posCalcSettings);
        $possession = $calculator->calculate($homeTeamStr, $awayTeamStr);

        $this->assertInstanceOf(Possession::class, $possession);
        $this->assertEquals(1, $possession->homeTeam + $possession->awayTeam);
        $this->assertTrue($possession->homeTeam === $possession->awayTeam);

    }

    /**
     * Generic test
     */
    public function testDifferentStrengths()
    {
        $posCalcSettings = new PossessionCalculatorSettings();
        $posCalcSettings->randomRange = 0; // no random in tests

        $str = 10;
        $homeTeamStr = $this->getSquadStrength($str);
        $awayTeamStr = $this->getSquadStrength(($str * 2));

        $calculator = new PossessionCalculator($posCalcSettings);
        $possession = $calculator->calculate($homeTeamStr, $awayTeamStr);

        $this->assertEquals(0.2, $possession->homeTeam);
        $this->assertEquals(0.8, $possession->awayTeam);
    }

    /**
     * Get SquadStrength for tests
     *
     * @param int $str
     * @return SquadStrength
     */
    private function getSquadStrength(int $str): SquadStrength
    {
        $squadStr = new SquadStrength;
        $squadStr->goalkeeper = $str;
        $squadStr->defence = $str;
        $squadStr->midfield = $str;
        $squadStr->attack = $str;

        return $squadStr;
    }
}