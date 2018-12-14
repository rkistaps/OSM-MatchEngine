<?php

namespace OSM\Interfaces;

interface PlayerBuilderParamInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return int
     */
    public function getAge(): int;

    /**
     * @return int
     */
    public function getSkill(): int;

    /**
     * @return string
     */
    public function getPosition(): string;

    /**
     * @return int
     */
    public function getEnergy(): int;
}