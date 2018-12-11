<?php

use rkistaps\Engine\Classes\MatchEngine;
use rkistaps\Engine\Exceptions\EngineException;
use rkistaps\Engine\Helpers\LineupBuilder;
use rkistaps\Engine\Structures\Coach;
use rkistaps\Engine\Structures\Tactics\PlayItWideTactic;
use rkistaps\Engine\Structures\Tactics\TowardsMiddle;
use rkistaps\Engine\Structures\Team;

// use composer autoloader
require 'vendor/autoload.php';

try {

    $container = new DI\Container();
    $matchEngine = $container->get(MatchEngine::class);

    $homeTeamLineup = LineupBuilder::buildRandomLineup(4, 4, 2);
    $homeTeamTactic = new TowardsMiddle();
    $homeTeam = new Team(uniqid(), $homeTeamLineup, $homeTeamTactic);

    $awayTeamLineup = LineupBuilder::buildRandomLineup(4, 4, 2);
    $awayTeamTactic = new PlayItWideTactic();
    $awayTeam = new Team(uniqid(), $awayTeamLineup, $awayTeamTactic);

    $coach = new Coach(Coach::SPECIALITY_ATT, 1);
    $homeTeam->setCoach($coach);

    $coach = new Coach(Coach::SPECIALITY_ATT, 1);
    $awayTeam->setCoach($coach);

    $match = $matchEngine->play($homeTeam, $awayTeam);
    $result = $match->getResult();

    echo $result->homeScore . ':' . $result->awayScore . PHP_EOL;

} catch (EngineException $e) {
    echo $e->getMessage();
} catch (Exception $e) {
    echo $e->getMessage();
}
