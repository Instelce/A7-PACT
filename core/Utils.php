<?php

namespace app\core;

use IntlDateFormatter;

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

    /**
     * Take a date string like that '2024-12-12' and convert it to a
     * french date like '12 décembre 2024'
     */
    public static function formatDate(string $date): string
    {
        return strftime('%d %B %Y', strtotime($date));
    }

    public static function formatTypeString(string $type)
    {
        return ucfirst(str_replace('_', ' ', $type));
    }
}