<?php

use rkistaps\Engine\Classes\Match;
use rkistaps\Engine\Exceptions\EngineException;
use rkistaps\Engine\Helpers\LineupBuilder;
use rkistaps\Engine\Structures\Coach;
use rkistaps\Engine\Structures\MatchSettings;
use rkistaps\Engine\Structures\Tactics\TowardsMiddle;
use rkistaps\Engine\Structures\Team;

// use composer autoloader
require 'vendor/autoload.php';

try {

    $container = new DI\Container();

    $homeTeamWins = $awayTeamWins = $draws = 0;

    $homeTeamLineup = LineupBuilder::buildRandomLineup(4, 4, 2);
    $homeTeamTactic = new TowardsMiddle();
    $homeTeam = new Team(uniqid(), $homeTeamLineup, $homeTeamTactic);

    $awayTeamLineup = LineupBuilder::buildRandomLineup(4, 4, 2);
    $awayTeamTactic = new TowardsMiddle();
    $awayTeam = new Team(uniqid(), $awayTeamLineup, $awayTeamTactic);

    $coach = new Coach(Coach::SPECIALITY_ATT, 1);
    $homeTeam->setCoach($coach);

    $coach = new Coach(Coach::SPECIALITY_ATT, 1);
    $awayTeam->setCoach($coach);

    /** @var MatchSettings $settings */
    $settings = $container->get(MatchSettings::class);
    $settings->performanceRandomRange = 0;
    $settings->hasHomeTeamBonus = false;

    $games = 1000;
    for ($i = 0; $i <= $games - 1; $i++) {
        /** @var Match $match */
        $match = $container->make(Match::class);

        $homeTeam->perform(0);
        $awayTeam->perform(0);

        $report = $match->play($homeTeam, $awayTeam, $settings);

        if ($report->homeScore > $report->awayScore) {
            $homeTeamWins++;
        } elseif ($report->homeScore < $report->awayScore) {
            $awayTeamWins++;
        } else {
            $draws++;
        }
    }

    echo 'Home team wins: ' . $homeTeamWins . PHP_EOL;
    echo 'Away team wins: ' . $awayTeamWins . PHP_EOL;
    echo 'Draws: ' . $draws . PHP_EOL;

} catch (EngineException $e) {
    echo $e->getMessage();
} catch (Exception $e) {
    echo $e->getMessage();
}
