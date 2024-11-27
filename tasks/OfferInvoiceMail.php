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
        $tomorrow = date("d", strtotime("+1 day"));

        if ($tomorrow == 1) {
            echo "Sending offer invoice mail...\n";
        }
    }

    public function schedule(): string
    {
        return "0 0 28-31 * *";
    }
}