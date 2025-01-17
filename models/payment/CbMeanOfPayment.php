<?php

namespace app\models\payment;

use app\core\DBModel;

class CbMeanOfPayment extends DBModel
{
    public int $payment_id = 0;
    public string $name = '';
    public string $card_number = '';
    public string $expiration_date = '';
    public string $cvv = '';

    public static function tableName(): string
    {
        return 'cb_mean_of_payment';
    }

    public function attributes(): array
    {
        return ['payment_id', 'name', 'card_number', 'expiration_date', 'cvv'];
    }

    public static function pk(): string
    {
        return 'payment_id';
    }

    public function rules(): array
    {
        return [
            'name' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 64]],
            'card_number' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 16]],
            'expiration_date' => [self::RULE_REQUIRED, self::RULE_EXP_DATE],
            'cvv' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 3]]
        ];
    }
}