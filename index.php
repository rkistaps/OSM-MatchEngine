<?php

// use composer autoloader
use rkistaps\Engine\Classes\MatchEngine;
use rkistaps\Engine\Exceptions\EngineException;
use rkistaps\Engine\Structures\MatchSettings;
use rkistaps\Engine\Structures\Player;
use rkistaps\Engine\Structures\Squad;

require 'vendor/autoload.php';

try {
    $homeTeam = Squad::fromArray([
        'lineup' => [
            [
                'attributes' => [
                    'id' => 1,
                    'skill' => 100,
                    'energy' => 100,
                    'position' => Player::POS_D
                ]
            ]
        ]
    ]);

    $awayTeam = Squad::fromArray([
        'lineup' => [
            [
                'attributes' => [
                    'id' => 1,
                    'skill' => 100,
                    'energy' => 100,
                    'position' => Player::POS_D
                ]
            ]
        ]
    ]);

    $settings = new MatchSettings();
    $matchEngine = new MatchEngine($settings);

    $result = $matchEngine->play($homeTeam, $awayTeam);
    $result->play();

    var_dump($result);
} catch (EngineException $e) {
    echo $e->getMessage();
}
