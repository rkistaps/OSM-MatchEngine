<?php

// use composer autoloader
use rkistaps\Engine\Classes\MatchEngine;
use rkistaps\Engine\Exceptions\EngineException;
use rkistaps\Engine\Helpers\LineupBuilder;
use rkistaps\Engine\Structures\Coach;
use rkistaps\Engine\Structures\MatchSettings;
use rkistaps\Engine\Structures\Tactics\DefaultTactic;
use rkistaps\Engine\Structures\Team;

require 'vendor/autoload.php';

try {
    $homeTeamLineup = LineupBuilder::buildRandomLineup(4,4,2);
    $homeTeamTactic = new DefaultTactic();
    $homeTeam = new Team($homeTeamLineup, $homeTeamTactic);

    $awayTeamLineup = LineupBuilder::buildRandomLineup(4,4,2);
    $awayTeamTactic = new DefaultTactic();
    $awayTeam = new Team($awayTeamLineup, $awayTeamTactic);

    $settings = new MatchSettings();
    $matchEngine = new MatchEngine($settings);

    $coach = new Coach(Coach::SPECIALITY_ATT);
    $homeTeam->setCoach($coach);

    $coach = new Coach(Coach::SPECIALITY_DEF);
    $awayTeam->setCoach($coach);

    $result = $matchEngine->play($homeTeam, $awayTeam);

    var_dump($result);
} catch (EngineException $e) {
    echo $e->getMessage();
}
