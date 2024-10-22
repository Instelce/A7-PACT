<?php

namespace app\core;

class Utils
{
    /**
     * Take an hour like that `1h30` and convert it to a float like `1.5`
     * @param string $hour
     * @return float
     */
    public static function convertHourToFloat(string $hour): float
    {
        $hour = explode('h', $hour);
        $hour = (float)$hour[0] + (float)$hour[1] / 60;
        return $hour;
    }
}