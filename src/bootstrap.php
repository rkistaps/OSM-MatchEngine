<?php


if (!function_exists('getContainer')) {
    /**
     * @return \DI\Container
     */
    function getContainer()
    {
        $container = new DI\Container();

        return $container;
    }
}