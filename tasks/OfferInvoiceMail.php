<?php

namespace app\tasks;

use app\core\CronTask;

/**
 * Send offer invoice mail at the end of the month
 */
class OfferInvoiceMail extends CronTask
{
    public function getName(): string
    {
        return "offer-invoice-mail";
    }

    public function getDescription(): string
    {
        return "Send offer invoice mail";
    }

    public function run(): void
    {
        echo "Sending offer invoice mail...\n";
    }

    public function runCondition(): bool
    {
        $tomorrow = date("d", strtotime("+1 day"));

        // Verify if the current day is the last day of the month
        return $tomorrow == 1;
    }

    public function schedule(): string
    {
        return "0 0 28-31 * *";
    }
}