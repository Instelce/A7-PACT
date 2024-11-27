<?php

namespace app\core;

/**
 * Abstract task to be run with a cron job
 *
 * First    - minute            (0 - 59)
 * Second   - hour              (0 - 23)
 * Third    - day of month      (1 - 31)
 * Fourth   - month             (1 - 12)
 * Fifth    - day of week       (0 - 7) (Sunday=0)
 */
abstract class CronTask
{
    /**
     * Get the task name
     */
    abstract public function getName(): string;

    /**
     * Get the task description
     */
    abstract public function getDescription(): string;

    /**
     * Run the task
     */
    abstract public function run(): void;

    /**
     * Schedule the task
     */
    abstract public function schedule(): string;

    public function scheduleString(): string
    {
        $string = "";
        $parts = explode(' ', $this->schedule());

        [$minute, $hour, $day, $month, $weekday] = $parts;

        $string .= $minute === '*' ? 'every minute ' : 'at minute ' . $minute . ' ';
        $string .= $hour === '*' ? 'every hour ' : 'at hour ' . $hour . ' ';
        $string .= $day === '*' ? 'every day ' : 'at day ' . $day . ' ';
        $string .= $month === '*' ? 'every month ' : 'at month ' . $month . ' ';
        $string .= $minute === '*' ? 'every weekday' : 'at weekday ' . $weekday . ' ';

        return $string;
    }
}