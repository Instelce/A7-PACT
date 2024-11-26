<?php

namespace app\models\payment;

use app\core\DBModel;

class Invoice extends DBModel
{
    public int $id = 0;
    public string $issue_date = '';
    public string $service_date = '';
    public string $due_date = '';
    public int $professional_id = 0;
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
        return ['issue_date', 'service_date', 'due_date', 'professional_id', 'offer_id'];
    }
}