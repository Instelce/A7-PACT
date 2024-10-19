<?php

namespace app\models\payment;

use app\core\DBModel;

class MeanOfPayment extends DBModel
{
    public int $payement_id = 0;
    public static function tableName(): string
    {
        return 'mean_of_payment';
    }

    public function attributes(): array
    {
        return [];
    }

    public static function pk(): string
    {
        return 'payment_id';
    }

    public function rules(): array
    {
        return [

        ];

    }
}