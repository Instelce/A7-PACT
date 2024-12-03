<?php

namespace app\tasks;

use app\core\CronTask;
use app\core\Utils;
use app\models\account\UserAccount;
use app\models\offer\Offer;
use app\models\payment\Invoice;

class OfferInvoiceCreation extends CronTask
{
    public function getName(): string
    {
        return "offer-invoice-creation";
    }

    public function getDescription(): string
    {
        return "Create an invoice for all the offers";
    }

    public function run(): void
    {
        /** @var Offer[] $offers */
        $offers = Offer::all();

        foreach ($offers as $offer) {

            // Check if the offer is owned by a private professional
            $user = $offer->professional();

            // Check if the offer is already invoiced
            $invoice = Invoice::findOne(['offer_id' => $offer->id, 'service_date' => date('m')]);
            $month = Utils::monthConversion(date('m'));

            if ($invoice) {
                echo "Invoice already created for $month for offer $offer->title ($offer->id) owned by $user->denomination\n";
                continue;
            }

            if ($user->isPrivate()) {
                $offer->addInvoice();
                echo "Invoice created for offer $offer->title ($offer->id) owned by $user->denomination\n";
            }
        }
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