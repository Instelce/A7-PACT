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

    public static function generateUUID() : string {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    public static function generateHash(): string
    {
        return hash('sha256', bin2hex(random_bytes(16)));
    }
}