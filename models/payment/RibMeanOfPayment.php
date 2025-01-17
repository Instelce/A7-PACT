<?php

namespace app\models\payment;

use app\core\DBModel;

class RibMeanOfPayment extends DBModel
{
    public int $payment_id = 0;
    public string $name = '';
    public string $iban = '';
    public string $bic = '';

    public static function tableName(): string
    {
        return 'rib_mean_of_payment';
    }

    public function attributes(): array
    {
        return ['payment_id', 'name', 'iban', 'bic'];
    }

    public static function pk(): string
    {
        return 'payment_id';
    }

    public function rules(): array
    {
        return [
            'titular_account' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 64]],
            'iban' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 34]],
            'bic' => [self::RULE_MAX, 'max' => 11]
        ];
    }
}