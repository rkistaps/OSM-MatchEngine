<?php

// use composer autoloader
use rkistaps\Engine\Classes\MatchEngine;
use rkistaps\Engine\Classes\PossessionCalculator;
use rkistaps\Engine\Exceptions\EngineException;
use rkistaps\Engine\Helpers\LineupBuilder;
use rkistaps\Engine\Structures\Coach;
use rkistaps\Engine\Structures\MatchSettings;
use rkistaps\Engine\Structures\Settings\PossessionCalculatorSettings;
use rkistaps\Engine\Structures\Tactics\PlayItWideTactic;
use rkistaps\Engine\Structures\Tactics\TowardsMiddle;
use rkistaps\Engine\Structures\Team;

require 'vendor/autoload.php';

try {
    $homeTeamLineup = LineupBuilder::buildRandomLineup(4, 4, 2);
    $homeTeamTactic = new TowardsMiddle();
    $homeTeam = new Team(uniqid(), $homeTeamLineup, $homeTeamTactic);

    $awayTeamLineup = LineupBuilder::buildRandomLineup(4, 4, 2);
    $awayTeamTactic = new PlayItWideTactic();
    $awayTeam = new Team(uniqid(), $awayTeamLineup, $awayTeamTactic);

    $calcSettings = new PossessionCalculatorSettings();
    $possessionCalculator = new PossessionCalculator($calcSettings);

    $settings = new MatchSettings();
    $matchEngine = new MatchEngine($settings, $possessionCalculator);

    $coach = new Coach(Coach::SPECIALITY_ATT, 1);
    $homeTeam->setCoach($coach);

    $coach = new Coach(Coach::SPECIALITY_ATT, 1);
    $awayTeam->setCoach($coach);

    $match = $matchEngine->play($homeTeam, $awayTeam);
    $result = $match->getResult();

    echo $result->homeScore . ':' . $result->awayScore . PHP_EOL;

} catch (EngineException $e) {
    echo $e->getMessage();
}
