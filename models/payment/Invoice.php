<?php

namespace app\models\payment;

use app\core\DBModel;
use app\models\offer\Offer;

class Invoice extends DBModel
{
    public int $id = 0;
    public string $issue_date = '';
    // Month number
    public string $service_date = '';
    public string $due_date = '';
    public int $offer_id = 0;

    public function rules(): array
    {
        return [];
    }

    public static function tableName(): string
    {
        return 'invoice';
    }

    public function attributes(): array
    {
        return ['issue_date', 'service_date', 'due_date', 'offer_id'];
    }

    public function offer()
    {
        return Offer::findOne(['id' => $this->offer_id]);
    }
}