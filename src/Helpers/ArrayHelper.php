<?php

namespace rkistaps\Engine\Helpers;

class ArrayHelper
{
    /**
     * Get item from array by key
     *
     * @param string $key
     * @param array $array
     * @param null $default
     * @return mixed|null
     */
    public static function get(string $key, array $array, $default = null)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }
}